<?php

namespace App\Http\Controllers;
use App\Mail\SuccessOrder;
use App\Models\ProfileAddress;
use App\Models\User;
use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

// use Spatie\ArrayToXml\ArrayToXml;


class CartController extends Controller
{
    public function index()
    {
        $data['page'] = 'basket';
        $data['user'] = $user = Auth::user();
        $data['address'] = ProfileAddress::where('user_id', $user->id)->get();
        $data['cart'] = session()->get('cart');

        return view('profile.orders.cart', $data);
    }

    public function updateCount(Request $request) {
        $post = $request->post();
        session()->put('cart.'.$post['id'].'.quantity', $post['qty']);
        $item =  session()->get('cart.'.$post['id']);

        $totalAmount = 0;
        foreach (session()->get('cart') as $id => $product) {
            if ($id == $post['id'] && !isset($product['price'])) {
                $item = Product::multiplicity()->find($id);
                session()->put('cart.'.$id, [
                    "art" => null,
                    "price" => $item->price,
                    "title" => $item->title,
                    "images" => $item->images,
                    "quantity" => $post['qty'],
                    "total" => $post['qty'] * $item->price
                ]);

                $totalAmount += $item->price * $post['qty'];
            } else
                $totalAmount += $product['price'] * $product['quantity'];
        }

        if (DB::table('cart')->where('user_id', Auth::id())->first())
            DB::table('cart')->where('user_id', Auth::id())->update(['json' =>  json_encode(session()->get('cart'))]);
        else
            DB::table('cart')->insert(['user_id' => Auth::id(), 'json' =>  json_encode(session()->get('cart'))]);

        return [
            'status' => 'success',
            'itemAmount' => number_format($item['price'] * $item['quantity'], 0, '.', ''),
            'totalAmount' => number_format($totalAmount, 0, '.', '')
        ];
    }

    public function loadMini() {
        $cart = session()->get('cart');

        return view('profile.components.mini-basket', compact('cart'));
    }


    public function create(Request $request) {
        $user = Auth::user();
        $data = $request->validate([
            'address_id' => 'required',
            'comment' => ''
        ]);

        $data['user_id'] = $user->id;
        $data['random'] = rand(100000, 999999);

        $order = Order::create($data);
        foreach (session()->get('cart') as $id => $product) {
            $percent = \App\Product::getMaxSaleToProduct($id, $product['price'], $product['quantity']);
            $price = $percent ? ($product['price'] - (($product['price'] * $percent) / 100)) * $product['quantity'] : $product['price'] * $product['quantity'];
            Order::orderProducts($order->id, $id, $product['quantity'], $price);
        }

        Mail::to([$user, 'info3@sotka-sem.ru'])->send(new SuccessOrder($order, Order::getOrderProducts($order->id), ProfileAddress::getByID($order->address_id), $user));

        $this->empty();

        return response()->redirectToRoute('profile.orders.success', ['random' => $data['random']]);
    }

    public function success(Request $request, $random) {
        $data['page'] = 'basket';
        $data['order'] = Order::where('random', $random)->first();
        $data['products'] = Order::getOrderProducts($data['order']->id);
        $user = User::where('id',$data['order']->user_id)->first();

        $user_address = DB::table('user_address')->where(['user_id' => $data['order']->user_id, 'id' => $data['order']->address_id])->first();
        $expr = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->select('products.title AS title',
                'products.id as prod_id',
                'order_products.order_id',
                'products.price as price',
                'order_products.qty as quantity',
                'products.oneC_7 as oneC_7')
            ->where('orders.id',   $data['order']->id)
            ->get();

        $order = [
            'order_info' => [
                'random' => $random,
                "order_date" =>$data['order']->created_at,
                "comment" => $data['order']->comment,
                "user_name" => $user->name,
                "user_phone" => $user->phone,
                "user_region" => @$user_address->region,
                "user_city" => @$user_address->city,
                "user_address" => @$user_address->address ? : 'Самовывоз',
                "user_house" => @$user_address->house,
                'id' => $user->id
            ],
        ];
        foreach ($expr as $exp ) {
            $order[] = [
                'product_'.$exp->prod_id => [
                    "title" => $exp->title,
                    "quantity" => $exp->quantity,
                    "price" => $exp->price,
                    "oneC_7" => $exp->oneC_7,
                ]
            ];

        }

        // $result = ArrayToXml::convert($order, [], true, 'UTF-8', '1.1', [], true);
        $date = date('Y-m-d',strtotime($data['order']->created_at));
        $datetime = date('H-m-s',strtotime($data['order']->created_at));
        Storage::disk('public')->put('orders/'.$date.'_'.$datetime.'_'.$random.'.json', json_encode($order));
        Log::channel('orders')->info('Record order '.$date.'_'.$datetime.'_'.$random.' exists', [Storage::disk('public')->exists('orders/'.$date.'_'.$datetime.'_'.$random.'.json')]);
        return view('profile.orders.success', $data);
    }

    public function delete(Request $request, $id=0) {
        session()->remove('cart.'.$id);

        if (DB::table('cart')->where('user_id', Auth::id())->first())
            DB::table('cart')->where('user_id', Auth::id())->update(['json' =>  json_encode(session()->get('cart'))]);
    }

    public function empty() {
        session()->remove('cart');
        DB::table('cart')->where('user_id', Auth::id())->delete();

        return response()->redirectTo('profile/orders/cart');
    }
}
