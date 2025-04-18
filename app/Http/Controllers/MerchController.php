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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function showPreorder(Request $request, Preorder $preorder)
    {
        $search = $request->get('q', '');
        $onlyOrdered = $request->get('with_checkouts', false);
        $currentCategory = PreorderCategory::where('id', $request->get('category', $preorder->categories()->first()?->id))->first();
        $categories = PreorderCategory::root()->whereBelongsTo($preorder)->with('childs')->get();
        if (!empty($search)) {
            $categoryIds = PreorderProduct::where('preorder_id', $preorder->id)
                ->where(function (Builder $query)  use ($search) {
                    return $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('barcode', 'like', '%' . $search . '%');
                })
                //->where('title', 'like', '%' . $search . '%')
                //->orWhere('barcode', 'like', '%' . $search . '%')
                ->pluck('preorder_category_id');

            if (count($categoryIds)) {
                $categoryIds = array_unique($categoryIds->toArray());
                $currentsubCategory = PreorderCategory::
                where('preorder_category_id', $currentCategory->id)
                    ->where('preorder_id', $preorder->id)
                    ->where('id', $request->get('subcategory', $categoryIds[0]))
                    ->first();
                $subCategories = PreorderCategory::whereIn('id', $categoryIds)->get();
                $products = PreorderProduct::where('preorder_id', $preorder->id)
                    ->where('preorder_category_id', $currentsubCategory->id ?? 0)
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%');
            } else {
                $currentsubCategory = PreorderCategory::where('id', 0)->first();
                $subCategories = $currentCategory->childs;
                $products = PreorderProduct::where('id', 0);
            }
        } else {
            $currentsubCategory = PreorderCategory::
                where('preorder_category_id', $currentCategory->id)
                    ->where('id', $request->get('subcategory', $currentCategory->childs()->first()->id))
                    ->first();
            $subCategories = $currentCategory->childs;

            $products = $currentsubCategory->products();
        }

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
                'paginator', 'search', 'subCategories'));
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
        //dd($preorder);
        try {
            $countFiles = count(json_decode($preorder->merch_file));
            $spreadsheet = IOFactory::load(storage_path() . '/app/public/' . json_decode($preorder->merch_file)[$countFiles-1]->download_link);
            $sheets = PreorderTableSheet::where('preorder_id', $preorder->id)->where('active', true)->with('markup')->get();

            foreach ($sheets as $sheet) {
                $concreteSheet = $spreadsheet->getSheetByName($sheet->title);

                $row = 1;
                while ($row < $concreteSheet->getHighestRow()) {
                    $barcodeLetter = $preorder->is_internal ? ($preorder->merch_barcode_field ?? 'A') : $sheet->markup->barcode;
                    //dump($preorder->is_internal, $sheet->markup->barcode, $barcodeLetter);
                    //dump($concreteSheet->getCell($barcodeLetter . $row)->getValue());
                    $notExistBarcode = (!is_numeric($concreteSheet->getCell($barcodeLetter . $row)->getValue()) ||
                                        is_null($concreteSheet->getCell($barcodeLetter . $row)->getValue()) ||
                                        (int)$concreteSheet->getCell($barcodeLetter . $row)->getValue() === 0);

                    if (
                        $notExistBarcode
                        || is_null($concreteSheet->getCell($sheet->markup->price . $row)->getValue())
                    ) {
                        $row++;
                        continue;
                    }
                    $barcode = $concreteSheet->getCell($barcodeLetter . $row)->getValue();
                    //dump($barcode);
                    $product = PreorderProduct::where('barcode', $barcode)->where('preorder_id', $preorder->id)->first();
                    //dump($product);
                    if (!$product) {
                        $row++;
                        continue;
                    }

                    $concreteSheet->setCellValue($preorder->merch_qty_field . $row, $product->getTotalQty() ?? '');
                    $row++;
                }
                //dump($concreteSheet);
            }

            $preorder->is_finished = true;
            $preorder->save();
            //dd($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . str_replace('\'', '-', Str::transliterate($preorder->title)) . '.xls"');
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        } catch (\Exception $exception) {
            dump($exception->getMessage());
            dump($exception->getTraceAsString());
            \Log::error($exception->getMessage());
            $preorder->is_finished = false;
            $preorder->save();
        }
    }

    public function closeFromFile(Preorder $preorder, Request $request)
    {
        //dd($request->table);
        $results = [];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->table);
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $results[$worksheet->getTitle()] = $worksheet->toArray();
        }
        // save memory
        $spreadsheet->__destruct();
        $spreadsheet = NULL;
        unset($spreadsheet);
        $data=[];
        if (is_array($results)) {
            foreach ($results as $key=>$result) {
                foreach ($result as $fileRow) {
                    if (empty($fileRow[1]) || $fileRow[1]== 'Штрихкод' || empty($fileRow[3])) continue;
                    $data[$fileRow[1]] = $fileRow[3];
                }
            }
        }

        try {
            $countFiles = count(json_decode($preorder->merch_file));
            $spreadsheet = IOFactory::load(storage_path() . '/app/public/' . json_decode($preorder->merch_file)[$countFiles-1]->download_link);
            $sheets = PreorderTableSheet::where('preorder_id', $preorder->id)->where('active', true)->with('markup')->get();

            foreach ($sheets as $sheet) {
                $concreteSheet = $spreadsheet->getSheetByName($sheet->title);

                $row = 1;
                while ($row < $concreteSheet->getHighestRow()) {
                    $barcodeLetter = $preorder->is_internal ? ($preorder->merch_barcode_field ?? 'A') : $sheet->markup->barcode;
                    //dd($preorder->is_internal, $sheet->markup->barcode, $barcodeLetter);
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
                    $qty = $data[$barcode] ?? false;
                    //dd($data);
                    if (!$qty) {
                        $row++;
                        continue;
                    }

                    $concreteSheet->setCellValue($preorder->merch_qty_field . $row, $qty ?? '');
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

    public function exportOneC($preorderId)
    {
        //$preorder = Preorder::with('preorderCheckouts.products.preorder_product', 'preorderCheckouts.user', )->where('id', $preorderId)->first();
        $orders = PreorderCheckout::where('preorder_id', $preorderId)->with('products.preorder_product', 'user', 'preorder')->get();
        if (count($orders ?? [])) {
            $orderJson = json_encode($orders);
            $datetime = date('d_m_Y-H_i_s');
            $filename = "preorders/export/{$datetime}_preorder_id-{$preorderId}.json";
            $disk = Storage::disk('public');
            $disk->put($filename, $orderJson);
            return response()->json(['success' => true, 'preorder' => $preorderId]);
        }
        return response()->json(['success' => false]);
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
        $search = \request()->get('q', '');

        $onlyOrdered = \request()->get('with_checkouts', false);
        \Debugbar::disable();
        $currentCategory = PreorderCategory::where('preorder_id', $preorder->id)->where('id', request()->get('subcategory'))->with('products')->first();
        $products = $currentCategory->products();
        if ($onlyOrdered)
            $products = $products->whereHas('checkouts', function ($query) {
                $query->havingRaw('COUNT(*) > 0');});
        if (!empty($search)) {
            $products->where(function (Builder $query)  use ($search) {
                return $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }
        $products = $products->paginate(15);
        return response()->view('merch.components.list.lazy', compact('preorder', 'products'));
    }
}
