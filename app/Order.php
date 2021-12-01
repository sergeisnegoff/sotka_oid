<?php

namespace App;

use App\Models\ProfileAddress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = ['user_id', 'random', 'comment', 'address_id', 'status'];

    public static function orderProducts($order_id, $product_id, $quantity, $price, $sale=0, $excepted=0, $price_change=0, $qty_change=0) {
        if (!is_null(DB::table('order_products')->where(['product_id' => $product_id, 'order_id' => $order_id])->first()))
            return DB::table('order_products')
                ->where(['product_id' => $product_id, 'order_id' => $order_id])
                ->update(['order_id' => $order_id, 'product_id' => $product_id, 'qty' => $quantity, 'price' => $price, 'sale' => $sale, 'excepted' => $excepted, 'price_changed' => !empty(trim($price_change)) ? $price_change : 0, 'qty_changed' => !empty(trim($qty_change)) ? $qty_change : 0]);
        else
            return DB::table('order_products')
                ->insert(['order_id' => $order_id, 'product_id' => $product_id, 'qty' => $quantity, 'price' => $price, 'sale' => $sale,  'excepted' => $excepted, 'price_changed' => !empty(trim($price_change)) ? $price_change : 0, 'qty_changed' => !empty(trim($qty_change)) ? $qty_change : 0]);
    }

    public static function getOrderProducts($order_id) {
        return DB::table('order_products')->where('order_id', $order_id)->orderBy('excepted', 'DESC')->get()->each(function ($item) {
            $item->info = Product::multiplicity()->where('id', $item->product_id)->first();
        });
    }

    public static function getUserCurrentOrders($id, $history=false) {
        $model = new self;

        $orders = DB::table($model->getTable())->where('user_id', $id);
        if ($history)
            $orders = $orders->where('status', '=', 'Shipped');
        else
            $orders = $orders->where('status', '!=', 'Shipped');

        $orders = $orders->orderBy('created_at', 'DESC')->paginate(15);

        foreach ($orders->items() as $item) {
            $item->products = self::getOrderProducts($item->id);
            $item->address = ProfileAddress::getByID($item->address_id);
        }

        return $orders;
    }
}
