<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Slider;
use App\Models\SliderBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{

    public function index(Request $request)
    {
        $data['bigSlider'] = Slider::orderBy('sorder')->get();
        $data['sliderBlocks'] = SliderBlock::orderBy('sorder')->get()->chunk(4);
        $categories = (new Product())->mainPageCategory();
        $brands = (new Product())->mainPageBrand();

        $seeds = Product::with(['category', 'subSpecification'])
            ->where('main_page', 1)->where('total', '!=', 0)->orderBy('id', "desc")->paginate(100);

        if (empty($seeds))
            if (!empty($categories))
                $seeds = Product::with('category')
                    ->whereIn('category_id', $categories)->where('total', '!=', 0)->orderBy('id', "desc")->paginate(100);
            elseif (!empty($brands))
                $seeds = Product::with('category')
                    ->whereIn('brand_id', [$brands->id])->where('total', '!=', 0)->orderBy('id', "desc")->paginate(100);

        if ($request->expectsJson()) {
            return response()->json($seeds);
        }

        $cartKeys = collect(session()->get('cart'))->keys();

        return view('home', $data, compact('seeds', 'cartKeys'));
    }

    public function addToCart(Request $request, $id)
    {
        $seeds = Product::multiplicity()->orderBy('created_at', 'asc')->find($id);
        if (!$seeds) {
            abort(404);
        }
        $cart = session()->get('cart');
        if (empty($cart[$id])) {
            $cart[$id] = [
                "art" => $seeds->art,
                "price" => $seeds->price,
                "new_price" => $seeds->new_price,
                "title" => $seeds->title,
                "images" => $seeds->images,
                "quantity" => $request->quantity,
                'total' => $seeds->total
            ];
            session()->put(compact('cart'));

            if (DB::table('cart')->where('user_id', Auth::id())->first())
                DB::table('cart')->where('user_id', Auth::id())->update(['json' => json_encode(session()->get('cart'))]);
            else
                DB::table('cart')->insert(['user_id' => Auth::id(), 'json' => json_encode(session()->get('cart'))]);
        } else {
            $cart = session()->get('cart');
            $cart[$id]['quantity'] += $request->quantity;

            session()->put(compact('cart'));
            if (DB::table('cart')->where('user_id', Auth::id())->first())
                DB::table('cart')->where('user_id', Auth::id())->update(['json' =>  json_encode(session()->get('cart'))]);
            else
                DB::table('cart')->insert(['user_id' => Auth::id(), 'json' =>  json_encode(session()->get('cart'))]);
        }
    }
    public function update(Request $request, $id)
    {
        if( $request->quantity)
        {
            $cart = session()->get('cart');
            $cart[$id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);

            if (DB::table('cart')->where('user_id', Auth::id())->first())
                DB::table('cart')->update(['json' =>  json_encode(session()->get('cart'))]);
            else
                DB::table('cart')->insert(['user_id' => Auth::id(), 'json' =>  json_encode(session()->get('cart'))]);

            session()->flash('success', 'Корзина обновлена');
        }
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            if (DB::table('cart')->where('user_id', Auth::id())->first())
                DB::table('cart')->where('user_id', Auth::id())->update(['json' =>  json_encode(session()->get('cart'))]);
            else
                DB::table('cart')->insert(['user_id' => Auth::id(), 'json' =>  json_encode(session()->get('cart'))]);
            session()->flash('success', 'Товар удален с корзины');
        }
    }

    /*public function import() {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/storage/import/products.xls');
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $filesRAW = Storage::allFiles('public/import/images');

        $files = [];
        foreach ($filesRAW as $key => &$file) {
            $files[$key] = pathinfo(str_replace('public/', '', $file), PATHINFO_FILENAME);
            $file = str_replace('public/', '', $file);
        }

        $specifications = ['L', 'M', 'N', 'O'];
        $similar = ['G', 'H', 'I'];
        foreach ($sheetData as $key => $data) {
            if ($key == 1 || empty($data['C'])) continue;

            $filePath = '';
            if (in_array(trim($data['F']), $files)) {
                $filePath = $filesRAW[array_search(trim($data['F']), $files)];
            }

            $categoryID = (Category::where('title', $data['D'])->first() ? Category::where('title', $data['D'])->first() : Category::create(['title' => $data['D']]));
            $subCategoryID = (Category::where('title', $data['E'])->first() ? Category::where('title', $data['E'])->first() : Category::create(['title' => $data['E'], 'parent_id' => $categoryID->id]));
            $brandID = (Brands::where('title', $data['K'])->first() ? Brands::where('title', $data['K'])->first() : Brands::create(['title' => $data['K']]));

            $id = Product::updateOrCreate(['title' => $data['C']], [
                'title' => $data['C'],
                'description' => $data['J'],
                'images' => !empty($filePath) ? $filePath : '',
                'category_id' => $subCategoryID->id,
                'brand_id' => $brandID->id,
                'oneC_7' => $data['A'],
                'oneC_8' => $data['B'],
            ]);

            Product::deleteSpecifications($id->id);

            foreach ($specifications as $specification) {
                Specification::firstOrCreate(['title' => $sheetData[1][$specification]], ['title' => $sheetData[1][$specification]]);

                $idSpecification = Subspecification::firstOrCreate(['title' => $data[$specification], 'specification' => $sheetData[1][$specification]], [
                    'title' => $data[$specification],
                    'specification' => $sheetData[1][$specification]
                ]);

                Product::addSpecification($id->id, $idSpecification->id);
            }

            Product::deleteSimilarProducts($id->id);
            foreach ($similar as $item) {
                if (!empty($data[$item])) {
                    Product::addSimilarProduct($id->id, $data[$item]);
                }
            }
        }
    }*/
}
