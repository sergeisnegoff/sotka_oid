<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Preorder\PreorderController;
use App\Models\Preorder;
use App\Models\PreorderCategory;
use App\Models\PreorderCheckout;
use App\Models\PreorderCheckoutProduct;
use App\Models\PreorderProduct;
use App\Models\PreorderSheetMarkup;
use App\Models\PreorderTableSheet;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;
use Mockery\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Str;
use Vtiful\Kernel\Excel;

class MerchController extends Controller
{
    public function home()
    {
        $preorders = Preorder::unfinished();
        $page = 'index';
        $preorders = $preorders->paginate(15);
        $paginator = $preorders;
        return response()->view('merch.index',
            compact('preorders', 'paginator', 'page'));
    }

    public function history()
    {
        $preorders = Preorder::finished();
        $page = 'history';
        $preorders = $preorders->paginate(15);
        $paginator = $preorders;
        return response()->view('merch.history',
            compact('preorders', 'paginator', 'page'));
    }

    public function showPreorder(Preorder $preorder)
    {
        $onlyOrdered = \request()->get('with_checkouts', false);
        $currentCategory = PreorderCategory::where('id', request()->get('category', $preorder->categories()->first()?->id))->first();
        $currentsubCategory = PreorderCategory::
        where('preorder_category_id', $currentCategory->id)
            ->where('id', request()->get('subcategory', $currentCategory->childs()->first()->id))
            ->first();
        $categories = PreorderCategory::root()->whereBelongsTo($preorder)->with('childs')->get();
        $products = $currentsubCategory->products();
        if ($onlyOrdered) {
            $products = $products->whereHas('checkouts', function ($query) {
                $query->havingRaw('COUNT(*) > 0');});
        }
        $products = $products->paginate(15);
        $paginator = $products;
        return response()->view('merch.preorder',
            compact('currentCategory',
                'onlyOrdered',
                'currentsubCategory',
                'categories',
                'preorder',
                'products',
                'paginator'));
    }

    public function changeQty(PreorderProduct $product)
    {
        $qty = request()->input('qty');
        $op = request()->input('operation');
        $isIncrement = $op === 'increment';
        DB::transaction(function () use ($isIncrement, $qty, $product) {
            while ($qty > 0) {
                if ($isIncrement) {
                    $qty = $this->_incrementQty($qty, $product);
                } else {
                    $qty = $this->_decrementQty($qty, $product);
                }
            }
        });
        return response()->view('merch.components.list.element', ['product' => $product, 'preorder' => $product->preorder]);
    }

    private function _incrementQty(int $qty, PreorderProduct $product): int
    {
        $existingCheckout = PreorderCheckout::where('user_id', auth()->user()->id)->where('preorder_id', $product->preorder_id)->first();
        $existingCheckoutProduct = PreorderCheckoutProduct::where('preorder_product_id', $product->id)->whereHas('preorderCheckout', function ($query) {
            $query->where('is_internal', true);
        })->first();
        if ($existingCheckoutProduct) {
            $existingCheckoutProduct->qty += $qty;
            $existingCheckoutProduct->save();
        } else {
            if (!$existingCheckout)
                $existingCheckout = PreorderCheckout::create([
                    'preorder_id' => $product->preorder->id,
                    'is_internal' => true,
                    'user_id' => auth()->user()->id
                ]);
            $newProductCheckout = PreorderCheckoutProduct::create([
                'preorder_product_id' => $product->id,
                'qty' => $qty,
                'preorder_checkout_id' => $existingCheckout->id
            ]);
        }
        return 0;
    }

    private function _decrementQty(int $qty, PreorderProduct $product): int
    {
        //пробуем найти заказ на сотку
        $existingSotkaCheckout = PreorderCheckoutProduct::where('preorder_product_id', $product->id)->whereHas('preorderCheckout', function ($query) {
            $query->where('is_internal', true);
        })->first();
        if ($existingSotkaCheckout)
            return $this->_decreaseProductQty($qty, $existingSotkaCheckout);
        else {
            $anyUserLastCheckout = PreorderCheckoutProduct::where('preorder_product_id', $product->id)
                ->orderByDesc('created_at')->first();
            return $this->_decreaseProductQty($qty, $anyUserLastCheckout);
        }
    }

    private function _decreaseProductQty(int $qty, ?PreorderCheckoutProduct $product): int
    {
        if (!$product) return 0;
        switch (true) {
            case $product->qty > $qty:
                $product->qty -= $qty;
                $product->save();
                return 0;
            case $product->qty <= $qty:
                $qty -= $product->qty;
                $product->delete();
                return $qty;
        }
        return $qty;
    }

    public function close(Preorder $preorder)
    {
        try {
            $spreadsheet = IOFactory::load(storage_path() . '/app/public/' . json_decode($preorder->merch_file)[0]->download_link);
            $sheets = PreorderTableSheet::where('preorder_id', $preorder->id)->where('active', true)->with('markup')->get();

            foreach ($sheets as $sheet) {
                $concreteSheet = $spreadsheet->getSheetByName($sheet->title);

                $row = 1;
                while ($row < $concreteSheet->getHighestRow()) {
                    $barcodeLetter = $preorder->is_internal ? ($preorder->merch_barcode_field ?? 'A') : $sheet->markup->barcode;
                    //dd($preorder->is_internal, $sheet->markup->barcode);
                    $notExistBarcode = (is_null($concreteSheet->getCell($barcodeLetter . $row)->getValue())
                        || (int)$concreteSheet->getCell($barcodeLetter . $row)->getValue() === 0);

                    if (
                        $notExistBarcode
                        || is_null($concreteSheet->getCell($sheet->markup->price . $row)->getValue())
                    ) {
                        $row++;
                        continue;
                    }
                    $barcode = $concreteSheet->getCell($barcodeLetter . $row)->getValue();
                    $product = PreorderProduct::where('barcode', $barcode)->first();

                    if (!$product) {
                        $row++;
                        continue;
                    }

                    $concreteSheet->setCellValue($preorder->merch_qty_field . $row, $product->getTotalQty() ?? '');
                    $row++;
                }
            }
            $preorder->is_finished = true;
            $preorder->save();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . Str::transliterate($preorder->title) . '.xls"');
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            $preorder->is_finished = false;
            $preorder->save();
        }
    }

    public function unclose(Preorder $preorder)
    {
        $preorder->is_finished = false;
        $preorder->save();
        return redirect()->route('merch.home');
    }

    public function getTable(Preorder $preorder)
    {
        return response()->view('merch.components.preorder-main-info', compact('preorder'));
    }

    public function lazyPages(Preorder $preorder)
    {
        $onlyOrdered = \request()->get('with_checkouts', false);
        \Debugbar::disable();
        $currentCategory = PreorderCategory::where('id', request()->get('subcategory'))->with('products')->first();
        $products = $currentCategory->products();
        if ($onlyOrdered)
            $products = $products->whereHas('checkouts', function ($query) {
                $query->havingRaw('COUNT(*) > 0');});
        $products = $products->paginate(15);
        return response()->view('merch.components.list.lazy', compact('preorder', 'products'));
    }
}
