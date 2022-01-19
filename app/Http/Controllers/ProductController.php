<?php

namespace App\Http\Controllers;

use App\ProductFilter;
use App\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use TCG\Voyager\Facades\Voyager;

class ProductController extends Controller
{
    public function index(Request $request, ProductFilter $filters)
    {
        $seeds = Product::multiplicity()->with(['category', 'subSpecification', 'subFilter'])->filter($filters);

        $seeds = Product::with(['category', 'subSpecification'])
            ->where('catalog_page', 1)->where('total', '!=', 0)->orderBy('id', "desc")->paginate(100);


//        dd($seeds);

//        $sort = explode('/', $request->sort);
//
//
//        $seeds = $seeds->orderBy(!empty($sort[1]) ? $sort[1] : 'title', !empty($sort[0]) ? $sort[0] : 'ASC')->where('quantity', '>', 0)->where('total', '!=', 0);

//        $seeds = $seeds->get();
        if (empty($seeds->items()))
            abort(404);
        if (!empty($request->attributeStyle)) {
            $dataAttr = session()->get($request->attributeStyle);
            $dataAttr = [
                "attributeStyle" => $request->attributeStyle,
            ];
            session()->put(compact('dataAttr'));
            return redirect()->back();
        }
        if ($request->expectsJson()) {
            return response()->json($seeds);
        }

        return view('products.index', compact('seeds'));
    }

    public function getProductsCat(Request $request, $cat, ProductFilter $filters)
    {
        $sort = isset($_GET['sort']) ? explode('/', $request->get('sort')) : ['', ''];

        $seeds = Product::multiplicity()->with(['category', 'subSpecification', 'subFilter'])->whereHas('category', function ($query) use ($cat) {
            $query->where('parent_id', $cat);
        })->filter($filters)
            ->select('products.*')
            ->join('categories AS c', 'c.id', '=', 'products.category_id')
            ->orderBy('c.sorder', 'ASC')->where('total', '!=', 0);

        $seeds = $seeds->get();

        if (empty($seeds))
            abort(404);

        if (!empty($request->attributeStyle)) {

            $dataAttr = session()->get($request->attributeStyle);
            $dataAttr = [
                "attributeStyle" => $request->attributeStyle,

            ];
            session()->put(compact('dataAttr'));
            return redirect()->back();
        }

        if ($request->expectsJson()) {
            return response()->json($seeds);
        }
        return view('products.index', compact('seeds'));
    }

    public function getProductsSubCat(Request $request, $cat, $subcat, ProductFilter $filters)
    {
        $sort = isset($_GET['sort']) ? explode('/', $request->get('sort')) : ['', ''];

        $seeds = Product::multiplicity()->with(['category', 'subSpecification', 'subFilter'])->whereHas('category', function ($query) use ($subcat) {
            $query->where('title', $subcat);
        })->filter($filters)->orderBy(!empty($sort[1]) ? $sort[1] : 'title', !empty($sort[0]) ? $sort[0] : 'ASC')->where('total', '!=', 0)->get();

        if (empty($seeds))
            abort(404);


        if (!empty($request->attributeStyle)) {

            $dataAttr = session()->get($request->attributeStyle);
            $dataAttr = [
                "attributeStyle" => $request->attributeStyle,

            ];
            session()->put(compact('dataAttr'));
            return redirect()->back();
        }

        if ($request->expectsJson()) {
            return response()->json($seeds);
        }

        $cartKeys = collect(session()->get('cart'))->keys();

        return view('products.index', compact('seeds', 'cartKeys'));
    }
    public function getProduct($id)
    {
        $seed = Product::multiplicity()->with(['category', 'subSpecification'])->where('id', $id)->first();
        $seeds = Product::multiplicity()->whereHas('category', function ($query) use ($seed) {
            $query->where('title', $seed->category->title);
        })->whereNotIn('id', [$seed->id])->paginate(5);
        session()->push('products.product', $seed->getKey());

        $seedsSession = session()->get('products.product');
        $seedsViewed = Product::multiplicity()->with(['category'])->where('id', '!=', $id)->find($seedsSession);

        $cartKeys = collect(session()->get('cart'))->keys();

        return view('products.product', compact('seed', 'seeds', 'seedsViewed', 'cartKeys'));
    }

    public function searchProducts(Request $request, ProductFilter $filters)
    {
        if (isset($request->products)) {
            $seeds = Product::multiplicity()->with(['category', 'subSpecification', 'subFilter'])->filter($filters)->where('total', '!=', 0);
            $sort = explode('/', $request->sort);
            $seeds = $seeds->orderBy(!empty($sort[1]) ? $sort[1] : 'title', !empty($sort[0]) ? $sort[0] : 'ASC')->where('title', 'LIKE', "%{$request->products}%");
            $seeds = $seeds->get();

            if (!empty($request->attributeStyle)) {
                $dataAttr = session()->get($request->attributeStyle);
                $dataAttr = [
                    "attributeStyle" => $request->attributeStyle,
                ];
                session()->put(compact('dataAttr'));
                return redirect()->back();
            }

            if ($request->ajax() && !$request->sort && !$request->radios && $request->isAjax) {
                if (isset($seeds)) {
                    return response()->view('components.search', compact('seeds'));
                    /*$count = 0;
                    $html = '<ul class="list-group search-drop">';
                    foreach ($seeds as $s) {
                        if ($count == 5) {
                            $html .= '  <li class="list-group-item d-flex justify-content-between align-items-center " style="margin:0">
                              <button class="btn" type="submit">  Посмотреть остальные</button>
                    </li>';
                            break;
                        };
                        $html .= ' <a href="/product/' . $s->id . '" >
                    <li class="list-group-item d-flex justify-content-between align-items-center " style="margin:0">
                                                 ' . $s->title . ' ';
                        if (!empty(Voyager::image($s->images))) {
                            $html .= '   <div class="image-parent">
                        <img src="' . thumbImg( $s->images, 30, 50) . '" class="img-fluid" alt="' . $s->title . '"></div>';
                        }
                        $html .= ' </li></a>';
                        $count++;
                    }
                    $html .= '</ul>';*/
                    //return response($html);
                }
            }

            $cartKeys = collect(session()->get('cart'))->keys();

            return view('products.index', compact('seeds', 'cartKeys'));

        }

    }
}
