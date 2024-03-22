<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Preorder;
use App\Models\PreorderCategory;
use App\Models\PreorderCheckout;
use App\Models\PreorderCheckoutProduct;
use App\Models\PreorderProduct;
use App\Models\PreorderSheetMarkup;
use App\Models\PreorderTableSheet;
use App\Models\User;
use App\Services\Preorder\PreorderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PreorderController extends Controller
{
    public function index()
    {
        $preorders = Preorder::whereDate('end_date', '>', now()->toDateString())->whereHas('categories')->get();
        $info = Page::where('slug', 'preorders')->first();

        return view('preorder.index', compact('preorders', 'info'));
    }

    public function category(int $id)
    {
        $categories = PreorderCategory::where('preorder_id', $id)->root()
            ->with('childs')
            ->get();
        $currentId = request()->get('category');
        $currentCategory = null;
        if (!$currentId) {
            $currentCategory = $categories[0];
        } else {
               $currentCategory = PreorderCategory::where('id', $currentId)
                   ->with('childs')
                   ->first();
        }
        $preorder = Preorder::find($id);

        PreorderService::setLatestPreorder($id);
        $cartKeys = collect(array_keys(PreorderService::getCart()));

        return view(
            view: 'preorder.category',
            data: compact([
                'categories',
                'currentCategory',
                'preorder',
                'cartKeys'
            ])
        );
    }

    public function products(int $id)
    {
        $category = PreorderCategory::with(['products', 'preorder'])
            ->where('id', $id)
            ->first();
        $parentCategory = null;
        $products = $category->products;
        if ($category->isRoot()) {
            $subCategories = $category->childs()->pluck('id');
            $products = PreorderProduct::whereIn('preorder_category_id', $subCategories)->get();
        } else {
            $parentCategory = $category->parent()->first();
        }

        $cartKeys = collect(array_keys(PreorderService::getCart()));

        return view('preorder.products', compact('category',
            'parentCategory',
            'products',
            'cartKeys'));
    }

    public function product(int $id)
    {
        $product = PreorderProduct::with(['category', 'category.parent'])
            ->where('id', $id)
            ->first();

        $cartKeys = collect(array_keys(PreorderService::getCart()));

        return view('preorder.product', compact('product', 'cartKeys'));
    }

    public function page(int $id)
    {
        $info = Preorder::find($id);

        $preorders = Preorder::query()
            ->where('id', '!=', $id)
            ->get();

        return view('preorder.page', compact('info', 'preorders'));
    }

    public function addToCart(Request $request)
    {
        $product = PreorderProduct::find($request->id);
        $cart = PreorderService::getFullCart();

        if (!empty($cart[$product->id])) {
            $quantity = $cart[$product->id]['quantity'] + $request->quantity;
        } else {
            $quantity = $request->quantity;
        }

        if (!is_null($product->hard_limit)) {
            $quantity = $quantity > $product->hard_limit ? $product->hard_limit : $quantity;
        }

        $cart[$product->id] = [
            'id' => $product->id,
            'name' => $product->title,
            'price' => $product->price,
            'quantity' => $quantity,
            'multiplicity' => $product->multiplicity,
            'image' => $product->image ?? $product->preorder->default_image,
            'preorder_id' => $product->category->preorder_id,
        ];

        PreorderService::setLatestPreorder($product->category->preorder_id);
        PreorderService::updateCart($cart);
    }

    public function addToUserCart($request, User $user) {
        $product = PreorderProduct::find($request->id);
        $cart = PreorderService::getFullUserCart($user->id);

        if (!empty($cart[$product->id])) {
            $quantity = $cart[$product->id]['quantity'] + $request->quantity;
        } else {
            $quantity = $request->quantity;
        }

        if (!is_null($product->hard_limit)) {
            $quantity = $quantity > $product->hard_limit ? $product->hard_limit : $quantity;
        }

        $cart[$product->id] = [
            'id' => $product->id,
            'name' => $product->title,
            'price' => $product->price,
            'quantity' => $quantity,
            'multiplicity' => $product->multiplicity,
            'image' => $product->image ?? $product->preorder->default_image,
            'preorder_id' => $product->category->preorder_id,
        ];

        PreorderService::setLatestUserPreorder($product->category->preorder_id, $user->id);
        PreorderService::updateUserCart($cart, $user->id);
    }

    public function removeFromCart(int $id)
    {
        PreorderService::removeFromCart($id);
    }

    public function history()
    {
        $page = 'preorder_history';
        $user = auth()->user();
        $cart = PreorderCheckout::forUser()->with('preorder', 'products', 'products.preorder_product')->get();

        return view('preorder.history', compact('cart', 'page', 'user'));
    }

    public function clientUpload()
    {
        $page = 'preorder_upload';
        $preorders = PreorderService::getActivePreorders()->whereNotNull('client_file')->whereNotNull('client_qty_field')->get();
        //$preorders = Preorder::where('end_date', '>', date('Y-m-d H:s:i', time()))->get();
        //dd(Carbon::now(), $preorders);

        return view('preorder.upload', compact('page', 'preorders'));
    }

    public function clientUploadPost()
    {
        $userToSaveFrom = User::find(request()->manager_user_id);
        $preorder = Preorder::find(\request()->preorder_id);
        $file = request()->file('file');
        $sheets = PreorderTableSheet::where('preorder_id', $preorder->id)->where('active', true)->get();
        $reader = IOFactory::createReaderForFile($file);
        $spreadsheet = $reader->load($file);

        $qtyField = $preorder->client_qty_field;
        $barcodeField = $preorder->merch_barcode_field ?? 'A';
        // Проходимся по каждому листу клиентского прайса
        $out = [];
        foreach ($sheets as $sheet) {
            $clientSheet = $spreadsheet->getSheetByName($sheet->title);
            $markup = PreorderSheetMarkup::where('preorder_table_sheet_id', $sheet->id)->first();

            $row = 1;
            $array = [];
            while ($row < $clientSheet->getHighestRow()) {
                //$barcode = $clientSheet->getCell($markup->barcode ?? 'G' . $row)->getValue();
                //dd($barcodeField, mb_ord($qtyField));
                $barcode = $clientSheet->getCell($barcodeField . $row)->getValue();

                (int)$qty = $clientSheet->getCell($qtyField . $row)->getValue();


                $array[$row]['barcode'] = $barcode;
                $array[$row]['qty'] = $qty;
                if (!$barcode || !$qty || !is_numeric($qty)) {
                    $row++;
                    continue;
                }
                $product = PreorderProduct::where('barcode', $barcode)->where('preorder_id', $preorder->id)->first();
                $array[$row]['product'] = $product;
                if (!$product) {
                    $row++;
                    continue;
                }
                $array[$row]['hard_limit'] = $product->hard_limit;
                if ($product->hard_limit === 0) {
                    $row++;
                    continue;
                }

                if ($qty < $product->multiplicity) {
                    $qty = $product->multiplicity;
                } elseif ($qty % $product->multiplicity != 0) {
                    $qty = ceil($qty / $product->multiplicity) * $product->multiplicity;
                }

                $result['id'] = $product->id;
                $result['qty'] = $qty;
                $req = \request();
                $req->id = $result['id'];
                $req->quantity = $result['qty'];
                if (!$userToSaveFrom)
                    $this->addToCart($req);
                else
                    $this->addToUserCart($req, $userToSaveFrom);
                $out[] = $result;
                $row++;
            }
        }
        //dd($array);
        if (!count($out)) return response()->json(['result' => "Обработка завершена. Выгружено 0 позиций"], 200);

        /*$preorderCheckout = PreorderCheckout::create([
            'user_id' => auth()->id(),
            'preorder_id' => $preorder->id
        ]);
        foreach ($out as $each) {

            $preorderCheckoutProduct = PreorderCheckoutProduct::create([
                'qty' => $each['qty'],
                'preorder_product_id' => $each['id'],
                'preorder_checkout_id' => $preorderCheckout->id
            ]);
        }*/
        return response( )->json(['result' => "Обработка завершена. Выгружено ". count($out) . ' позиций']);
    }

    public function managerSummaryTable(Preorder $preorder) {
        PreorderService::createSummary($preorder);
    }
}
