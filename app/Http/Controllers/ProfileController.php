<?php

namespace App\Http\Controllers;

use App\Exports\ExportXls;
use App\Exports\TcpdfOrder;
use App\Mail\AccountAcepted;
use App\Models\Order;
use App\Models\ProfileAddress;
use App\Models\User;
use App\Models\UserBrandSaleSystem;
use App\Models\UserSaleSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }


    public function index()
    {
        $data['page'] = 'index';
        $data['user'] = $user = Auth::user();
        $data['address'] = ProfileAddress::where('user_id', $user->id)->get();

        return view('profile.index', $data);
    }

    /*
     * POST METHOD TO UPDATE
     * */
    public function update(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = Auth::user();
            $data = $request->validate([
                'name' => 'required',
                'city' => 'required',
                'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)]
            ]);


            foreach ($data as $key => $item)
                $user->$key = $item;

            $user->save();
        }

        return response()->redirectToRoute('profile.index');
    }

    /*
     * POST METHOD TO CHANGE PASSWORD
     * */
    public function changePassword(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'old_password' => 'required',
                'password' => 'required|confirmed|min:8',
            ]);

            if (Hash::check($data['old_password'], Auth::user()->password)) {
                User::find($id)->update(['password' => Hash::make($data['password'])]);
            }
        }

        return response()->redirectToRoute('profile.index');
    }

    protected function kladarQuery(array $input)
    {
        return collect(
            Http::get(
                'https://kladr-api.ru/api.php',
                array_merge(
                    config('services.kladar'),
                    $input
                )
            )
                ->json('result')
        )
            ->map(function ($item) {
                return json_decode(json_encode($item));
            })
            ->filter(function ($item) {
                return $item->id != 'Free';
            })
            ->values();
    }

    public function address(Request $request, $id = 0)
    {
        $action = $request->segment('3');


        switch ($action) {
            case 'create':
                $data['user'] = Auth::user();
                return \response()->view('profile.components.address', $data);
                break;
            case 'store':
                $data = $request->validate([
                    'city' => 'required',
                    'city_id' => 'required',
                    'address' => 'required',
                    'address_id' => 'required',
                    'region' => 'required',
                    'region_id' => 'required',
                    'house' => 'required|max:4',
                    'user_id' => 'required'
                ]);
                ProfileAddress::store($data);
                break;
            case 'edit':
                $data['user'] = Auth::user();
                $data['item'] = ProfileAddress::find($id);
                return \response()->view('profile.components.address', $data);
                break;
            case 'update':
                $item = ProfileAddress::find($id);


                $data = $request->validate([
                    'city' => 'required',
                    'city_id' => 'required',
                    'address' => 'required',
                    'address_id' => 'required',
                    'region' => 'required',
                    'region_id' => 'required',
                    'house' => 'required|max:4',
                    'user_id' => 'required'
                ]);

                foreach ($data as $key => $value)
                    $item->$key = $value;

                $item->save();
                break;

            case 'delete':
                $item = ProfileAddress::find($id);

                $item->delete();
                break;
            case 'change':
                $user = Auth::user();

                $user->address = $id;
                $user->save();
                break;
            case 'autocomplete':
                if (!empty($request->get('s')) && !empty($request->get('type') == 'region')) {
                    try {
                        $items = $this->kladarQuery(
                            [
                                'query' => $request->get('s'),
                                'contentType' => 'region',
                            ]
                        )->map(function ($item) {
                            switch ($item->type) {
                                case 'Область':
                                    $name = $item->name . ' область';
                                    break;
                                case 'Край':
                                    $name = $item->name . ' край';
                                    break;
                                case 'Республика':
                                    $name = 'Республика ' . $item->name;
                                    break;
                                case 'Автономный округ':
                                    $name = $item->name . ' автономный округ';
                                    break;
                                case 'Автономная область':
                                    $name = $item->name . ' автономная область';
                                    break;
                                default:
                                    $name = $item->name;
                            }
                            return [
                                'id' => $item->id,
                                'name' => $name,
                                "city" => true,
                                "type" => $item->type
                            ];
                        });

                        if (collect($items)->isEmpty()) {
                            throw new \Exception();
                        }
                        return \response()->json($items);
                    } catch (\Exception $e) {
                        return \response()->json(['status' => 'error', 'msg' => 'К сожалению, данный регион не найден']);
                    }
                } elseif (!empty($request->get('regionId')) && (!empty($request->get('s')))) {
                    try {
                        $items = $this->kladarQuery(
                            [
                                'regionId' => $request->get('regionId'),
                                'query' => $request->get('s'),
                                'contentType' => 'city',
                                'withParent' => 'true',
                            ]
                        )->filter(function ($item) {
                            return $item->id != 'Free';
                        })->map(function ($item) use (&$items) {
                            $district = collect($item->parents)->where('contentType', 'district')->first();
                            return [
                                'id' => $item->id,
                                'name' => ($district ? "$district->typeShort $district->name, " : '') . $item->typeShort . ' ' . $item->name,
                                "city" => true,
                            ];
                        });

                        if (collect($items)->isEmpty()) {
                            throw new \Exception();
                        }

                        return \response()->json($items);
                    } catch (\Exception $e) {
                        return \response()->json(['status' => 'error', 'msg' => 'К сожалению, данный город не найден']);
                    }
                } elseif (!empty($request->get('cityId')) && (!empty($request->get('s')))) {
                    try {
                        $items = $this->kladarQuery(
                            [
                                'cityId' => $request->get('cityId'),
                                'query' => $request->get('s'),
                                'contentType' => 'street',
                            ]
                        )->filter(function ($item) {
                            return $item->id != 'Free';
                        })->map(function ($item) use (&$items) {
                            return [
                                'id' => $item->id,
                                'name' => $item->typeShort . ' ' . $item->name,
                                'city' => false
                            ];
                        });

                        if (collect($items)->isEmpty()) {
                            throw new \Exception();
                        }

                        return \response()->json($items);
                    } catch (\Exception $e) {
                        return \response()->json(['status' => 'error', 'msg' => 'К сожалению, данный адрес не найден']);
                    }
                } else {
                    try {
                        $items = $this->kladarQuery(
                            [
                                'streetId' => $request->get('streetId'),
                                'query' => $request->get('s'),
                                'contentType' => 'building',
                            ]
                        )->filter(function ($item) {
                            return $item->id != 'Free';
                        })->map(function ($item) use (&$items) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                "city" => true
                            ];
                        });


                        if (collect($items)->isEmpty()) {
                            throw new \Exception();
                        }

                        return \response()->json($items);
                    } catch (\Exception $e) {
                        return \response()->json(['status' => 'error', 'msg' => 'К сожалению, данный адрес не найден']);
                    }
                }
                return response()->redirectToRoute('profile.index');
        }

        return \redirect()->back();
    }

    public function orders(Request $request)
    {
        $action = $request->segment(3);
        $data = [];

        switch ($action) {
            case 'current':
                $data['page'] = 'current-orders';
                $data['orders'] = Order::getUserCurrentOrders($request->user()->id);
                break;
            case 'order-history':
                $data['page'] = 'order-history';
                $data['orders'] = Order::getUserCurrentOrders($request->user()->id, true);
                break;
        }

        return \response()->view('profile.orders.current', $data);
    }

    public function reOrders($id)
    {

        $expr = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('user_address', 'user_address.user_id', '=', 'users.id')
            ->select('products.title AS title',
                'products.images as images',
                'products.total as total',
                'orders.created_at as order_date',
                'products.id',
                'order_products.order_id',
                'users.name as u_name',
                'users.email as u_email',
                'users.phone as u_phone',
                'user_address.city as u_city',
                'user_address.address as u_address',
                'products.price as price',
                'order_products.qty as quantity')
            ->where('orders.id', $id)
            ->where('order_products.qty', '>', 0)
            ->get();

//        dd($expr);

        $cart = [];
        foreach ($expr as $exp) {
            $cart[$exp->id] = [
                "title" => $exp->title,
                "images" => $exp->images,
                "quantity" => $exp->quantity,
                "price" => $exp->price,
                "total" => $exp->total
            ];
        }
        session()->put(compact('cart'));

        if (DB::table('cart')->where('user_id', Auth::id())->first())
            DB::table('cart')->where('user_id', Auth::id())->update(['json' => json_encode(session()->get('cart'))]);
        else
            DB::table('cart')->insert(['user_id' => Auth::id(), 'json' => json_encode(session()->get('cart'))]);

        $cart = session()->get('cart');

        return Redirect::route('profile.orders.cart', ['id' => $id]);
//        return redirect()->back()->with('success', 'Товар добавлен в корзину');
    }

    public function orderHistory(Request $request)
    {
        $action = $request->segment(3);
        $data['page'] = 'order-history';

        $data['orders'] = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->select('products.title AS title',
                'products.id as prod_id',
                'order_products.order_id',
                'orders.user_id',
                'orders.random',
                'orders.status',
                'orders.id as order_id',
                'orders.created_at',
                'products.price as price',
                'products.title',
                'products.images',
                'order_products.qty as quantity',
                'products.oneC_7 as oneC_7')
            ->where('orders.user_id', Auth::id())
            ->get();

        return \response()->view('profile.orders.' . $action, $data);

    }

    public function updateTableCategories(Request $request, $id)
    {
        $data = \Illuminate\Support\Facades\Request::post('userRange');
        $categoriesCheckBox = \Illuminate\Support\Facades\Request::post('category');
        $categoryChilds = \Illuminate\Support\Facades\Request::post('category_childs');

        UserSaleSystem::where('user_id', $id)->delete();
        User::removeUserSales($id);

        foreach ($data as $category_id => $percent) {
            if (in_array($category_id, array_keys($categoriesCheckBox)))
                User::addSaleToCategory($category_id, $percent, $id);

            foreach ($categoriesCheckBox as $categoryCheck_id => $checkBox)
                if (in_array($categoryCheck_id, explode(',', $categoryChilds[$category_id])))
                    User::addSaleToCategory($categoryCheck_id, $percent, $id);

            if (!empty($percent))
                UserSaleSystem::create(['sale' => (float)$percent, 'category_id' => $category_id, 'user_id' => $id]);
        }
        return Redirect::route('voyager.users.edit', ['id' => $id]);
    }

    public function updateTableBrands(Request $request, $id)
    {
        $data = \Illuminate\Support\Facades\Request::post('priceRange');
        $brandsCheckBox = \Illuminate\Support\Facades\Request::post('brands');

        UserBrandSaleSystem::where('user_id', $id)->delete();
        (new \App\Models\Brands)->removeBrandSalesToUser($id);

        foreach ($data as $brand_id => $percent) {
            if (in_array($brand_id, array_keys($brandsCheckBox)))
                User::addSaleToBrand($brand_id, $percent, $id);

            if (!empty($percent))
                UserBrandSaleSystem::create(['sale' => (float)$percent, 'brand_id' => $brand_id, 'user_id' => $id]);
        }

        return Redirect::route('voyager.users.edit', ['id' => $id]);
    }

    public function activeAccount(Request $request, $id)
    {
        if ($request->active) {
            $userData = User::find($id);
            $userData->active = $request->active;
            $userData->save();

            if ($request->active == 'on') {
                Mail::to($userData->email)->send(new AccountAcepted());
            }

            return json_encode(array('statusCode' => 200));
        }
    }

    public function exportPdf(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }
        $order->load(['products']);
        if ($order instanceof \App\Models\Order) {
            ob_end_clean();
            return new TcpdfOrder($order, TcpdfOrder::TYPE_PRINT);
        }
        return $order;
    }

    public function exportXls(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        $order->load(['products']);
        if ($order instanceof \App\Models\Order) {
            return Excel::download(new ExportXls($order), 'order.xlsx');
        }
        return $order;
    }
}
