<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use TeamTNT\TNTSearch\TNTSearch;

class ProductController extends Controller {
    public function index(Request $request, ProductFilter $filters) {
        $seeds = Product::multiplicity()->with(['category', 'subSpecification', 'subFilter'])->filter($filters);

        $seeds = Product::with(['category', 'subSpecification'])
            ->where('catalog_page', 1)->where('total', '!=', 0)->orderBy('id', "desc")->paginate(100);

        $cats = Category::whereHas('product', function ($query) {
            $query->where('catalog_page', 1);
        })->get();


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

        return view('products.index', compact('seeds', 'cats'));
    }

    public function getProductsCat(Request $request, $cat, ProductFilter $filters) {

        $sort = isset($_GET['sort']) ? explode('/', $request->get('sort')) : ['', ''];

        $limitProductsPerCategory = 4;

//        $cats = Cache::remember(
//            __CLASS__ . '::' . __FUNCTION__ . '@' . json_encode(compact('limitProductsPerCategory', 'filters', 'sort')),
//            60,
//            function () use ($limitProductsPerCategory, $cat, $filters, $sort) {
//                $subPipeMain = function ($query) use ($filters, $sort) {
//                    return $query
//                        ->where('total', '!=', 0)
//                        ->scopes(['filter' => [$filters]])
//                        ->orderBy(!empty($sort[1]) ? $sort[1] : 'title', !empty($sort[0]) ? $sort[0] : 'ASC');
//                };
//                $subPipeWith = function ($query) {
//                    return $query
//                        ->with(['category', 'subSpecification', 'subFilter']);
//                };
//
//                $pipe = function ($query) use ($subPipeMain, $subPipeWith) {
//                    $subPipeMain($query);
//                    return $subPipeWith($query);
//                };
//
//                return optional(
//                    Category::query()->where('title', $cat)
//                        ->whereHas('category.product', $pipe)
//                        ->with(
//                            [
//                                'category.product' => function ($query) use ($subPipeWith, $subPipeMain, $limitProductsPerCategory) {
//                                    /** @var Builder $query */
//                                    $table = $query->getModel()->getTable();
//                                    $sub = Product::query()
//                                        ->when(true, $subPipeMain)
//                                        ->toBase();
//
//                                    $orders = collect($sub->orders)->map(function($item) {
//                                        return "`{$item['column']}` " . strtoupper($item['direction']);
//                                    })->join(', ');
//                                    if($orders) {
//                                        $orders = " ORDER BY $orders";
//                                    }
//                                    $sub->orders = null;
//                                    $sub->selectRaw("CONCAT(';', REGEXP_SUBSTR(GROUP_CONCAT(`id`$orders SEPARATOR ';'), '^\\\\d+(;\\\\d+)?(;\\\\d+)?(;\\\\d+)?'), ';') as ids");
//                                    $sub->groupBy('category_id');
//
//                                    $query
//                                        ->joinSub($sub,
//                                            'prod_relations',
//                                            'prod_relations.ids',
//                                            'LIKE',
//                                            DB::raw("CONCAT('%;', `$table`.`id`, ';%')")
//                                        )
//                                        ->when(true, $subPipeWith);
//                                },
//                            ]
//                        )
//                        ->first()
//                )->category;
//            }
//        );

        $subPipeMain = function ($query) use ($filters, $sort) {
            return $query
                ->where('total', '!=', 0)
                ->scopes(['filter' => [$filters]])
                ->orderBy(!empty($sort[1]) ? $sort[1] : 'title', !empty($sort[0]) ? $sort[0] : 'ASC');
        };
        $subPipeWith = function ($query) {
            return $query
                ->with(['category', 'subSpecification', 'subFilter']);
        };

        $pipe = function ($query) use ($subPipeMain, $subPipeWith) {
            $subPipeMain($query);
            return $subPipeWith($query);
        };

        $cats = optional(
            Category::query()->where('title', $cat)
                ->whereHas('category.product', $pipe)
                ->with(
                    [
                        'category.product' => function ($query) use ($subPipeWith, $subPipeMain, $limitProductsPerCategory) {
                            /** @var Builder $query */
                            $table = $query->getModel()->getTable();
                            $sub = Product::query()
                                ->when(true, $subPipeMain)
                                ->toBase();

                            $orders = collect($sub->orders)->map(function($item) {
                                return "`{$item['column']}` " . strtoupper($item['direction']);
                            })->join(', ');
                            if($orders) {
                                $orders = " ORDER BY $orders";
                            }
                            $sub->orders = null;
                            $sub->selectRaw("CONCAT(';', REGEXP_SUBSTR(GROUP_CONCAT(`id`$orders SEPARATOR ';'), '^\\\\d+(;\\\\d+)?(;\\\\d+)?(;\\\\d+)?'), ';') as ids");
                            $sub->groupBy('category_id');

                            $query
                                ->joinSub($sub,
                                    'prod_relations',
                                    'prod_relations.ids',
                                    'LIKE',
                                    DB::raw("CONCAT('%;', `$table`.`id`, ';%')")
                                )
                                ->when(true, $subPipeWith);
                        },
                    ]
                )
                ->first()
        )->category;

        if (is_null($cats))
            abort(404);

        $cats = $cats->filter(function (Category $category) {
            return $category->product->isNotEmpty();
        });

        if (!empty($request->attributeStyle)) {

            $dataAttr = [
                "attributeStyle" => $request->attributeStyle,
            ];
            session()->put(compact('dataAttr'));
            return redirect()->back();
        }

        return view('products.index', compact('cats'));
    }

    public function getProductsSubCat(Request $request, $cat, $subcat, ProductFilter $filters) {
        $sort = isset($_GET['sort']) ? explode('/', $request->get('sort')) : ['', ''];

        $seeds = Product::multiplicity()->with(['category', 'subSpecification', 'subFilter'])->whereHas(
            'category',
            function ($query) use ($subcat) {
                $query->where('title', $subcat);
            }
        )->filter($filters)->orderBy(!empty($sort[1]) ? $sort[1] : 'title', !empty($sort[0]) ? $sort[0] : 'ASC')->where(
            'total',
            '!=',
            0
        )->get();

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

    public function getProduct($id) {
        $seed = Product::multiplicity()->with(['category', 'subSpecification'])->where('id', $id)->first();

        if (is_null($seed)) {
            abort(404);
        }

        $seeds = Product::multiplicity()->where('total', '!=', 0)->whereHas('category', function ($query) use ($seed) {
            $query->where('title', $seed->category?->title);
        })->whereNotIn('id', [$seed->id])->paginate(5);
        session()->push('products.product', $seed->getKey());

        $cat = Category::where('id', $seed->category->parent_id)->first();

        $seedsSession = session()->get('products.product');
        $seedsViewed = Product::multiplicity()->with(['category'])->where('id', '!=', $id)->find($seedsSession);

        $cartKeys = collect(session()->get('cart'))->keys();

        return view('products.product', compact('seed', 'seeds', 'seedsViewed', 'cartKeys', 'cat'));
    }

    public function searchProducts(Request $request, ProductFilter $filters)
    {
        if (!$request->has('products')) {
            return redirect()->route('products.index');
        }

        $searchQuery = $request->products;
        if (empty($searchQuery)) {
            $seeds = Product::multiplicity()
                ->with(['category', 'subSpecification', 'subFilter'])
                ->filter($filters)
                ->where('total', '!=', 0)->get();

        } else {
            try {
                // Простой поиск без сложного форматирования
                $seeds = Product::search($searchQuery)
                    ->query(function ($query) use ($filters) {
                        return $query->multiplicity()
                            ->with(['category', 'subSpecification', 'subFilter'])
                            ->filter($filters)
                            ->where('total', '!=', 0);
                    });
                $sort = explode('/', $request->sort);
                $seeds = $seeds->orderBy(
                    !empty($sort[1]) ? $sort[1] : 'title',
                    !empty($sort[0]) ? $sort[0] : 'ASC'
                );

                $seeds = $seeds->get();

                if (!count($seeds)) {
                    $seeds = Product::multiplicity()
                        ->with(['category', 'subSpecification', 'subFilter'])
                        ->filter($filters)
                        ->where('total', '!=', 0)
                        ->where('title', 'LIKE', "%{$searchQuery}%");
                    $sort = explode('/', $request->sort);
                    $seeds = $seeds->orderBy(
                        !empty($sort[1]) ? $sort[1] : 'title',
                        !empty($sort[0]) ? $sort[0] : 'ASC'
                    );

                    $seeds = $seeds->get();
                }
            } catch (\Exception $e) {
                Log::error('Ошибка поиска: ' . $e->getMessage());
                //dd($e->getMessage());
                $seeds = Product::multiplicity()
                    ->with(['category', 'subSpecification', 'subFilter'])
                    ->filter($filters)
                    ->where('total', '!=', 0)
                    ->where('title', 'LIKE', "%{$searchQuery}%");
                $sort = explode('/', $request->sort);
                $seeds = $seeds->orderBy(
                    !empty($sort[1]) ? $sort[1] : 'title',
                    !empty($sort[0]) ? $sort[0] : 'ASC'
                );

                $seeds = $seeds->get();
            }
        }

        if ($request->ajax() && !$request->sort && !$request->radios && $request->isAjax) {
            return response()->view('components.search', compact('seeds'));
        }

        $cartKeys = collect(session()->get('cart'))->keys();

        return view('products.index', compact('seeds', 'cartKeys'));
    }

//    public function searchProducts(Request $request, ProductFilter $filters)
//    {
//        if (isset($request->products)) {
//            $seeds = Product::multiplicity()
//                ->with(['category', 'subSpecification', 'subFilter'])
//                ->filter($filters)
//                ->where('total', '!=', 0);
//
//            $sort = explode('/', $request->sort);
//            $seeds = $seeds->orderBy(
//                !empty($sort[1]) ? $sort[1] : 'title',
//                !empty($sort[0]) ? $sort[0] : 'ASC'
//            );
//
//            if ($request->has('products') && !empty($request->products)) {
//                $searchQuery = collect(explode(' ', $request->products))
//                    ->filter()
//                    ->map(function($term) {
//                        return '+' . $term;
//                    })
//                    ->implode(' ');
//                //dd($searchQuery);
//                $seeds = Product::search($searchQuery)
//                    ->query(function ($query) use ($seeds) {
//                        return $query->mergeConstraintsFrom($seeds);
//                    })->get();
//            }
//            if ($request->ajax() && !$request->sort && !$request->radios && $request->isAjax) {
//                if (isset($seeds)) {
//                    return response()->view('components.search', compact('seeds'));
//                }
//            }
//
//            $cartKeys = collect(session()->get('cart'))->keys();
//
//            return view('products.index', compact('seeds', 'cartKeys'));
//        }
//    }

//    public function searchProducts(Request $request, ProductFilter $filters) {
//        if (isset($request->products)) {
//            $seeds = Product::multiplicity()
//                ->with(['category', 'subSpecification', 'subFilter'])
//                ->filter($filters)
//                ->where('total', '!=', 0);
//            $sort = explode('/', $request->sort);
//            $seeds = $seeds->orderBy(!empty($sort[1]) ? $sort[1] : 'title', !empty($sort[0]) ? $sort[0] : 'ASC')->where(
//                'title',
//                'LIKE',
//                "%{$request->products}%"
//            );
//            $seeds = $seeds->get();
//
//            if (!empty($request->attributeStyle)) {
//                $dataAttr = session()->get($request->attributeStyle);
//                $dataAttr = [
//                    "attributeStyle" => $request->attributeStyle,
//                ];
//                session()->put(compact('dataAttr'));
//                return redirect()->back();
//            }
//
//            if ($request->ajax() && !$request->sort && !$request->radios && $request->isAjax) {
//                if (isset($seeds)) {
//                    return response()->view('components.search', compact('seeds'));
//                }
//            }
//
//            $cartKeys = collect(session()->get('cart'))->keys();
//
//            return view('products.index', compact('seeds', 'cartKeys'));
//
//        }
//
//    }
}
