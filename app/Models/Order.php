<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use function app;
use function collect;


class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = ['user_id', 'comment', 'address_id', 'status', 'updated_status'];

    public static function orderProducts($order_id, $product_id, $quantity, $price, $sale = 0, $excepted = 0, $price_change = 0, $qty_change = 0)
    {
        if (!is_null(DB::table('order_products')->where(['product_id' => $product_id, 'order_id' => $order_id])->first()))
            return DB::table('order_products')
                ->where(['product_id' => $product_id, 'order_id' => $order_id])
                ->update(['order_id' => $order_id, 'product_id' => $product_id, 'qty' => $quantity, 'price' => $price, 'sale' => $sale, 'excepted' => $excepted, 'price_changed' => !empty(trim($price_change)) ? $price_change : 0, 'qty_changed' => !empty(trim($qty_change)) ? $qty_change : 0]);
        else
            return DB::table('order_products')
                ->insert(['order_id' => $order_id, 'product_id' => $product_id, 'qty' => $quantity, 'price' => $price, 'sale' => $sale, 'excepted' => $excepted, 'price_changed' => !empty(trim($price_change)) ? $price_change : 0, 'qty_changed' => !empty(trim($qty_change)) ? $qty_change : 0]);
    }

    public static function getOrderProducts($order_id)
    {
        return DB::table('order_products')->where('order_id', $order_id)->orderBy('excepted', 'DESC')->get()->each(function ($item) {
            $item->info = Product::multiplicity()->where('id', $item->product_id)->first();
        });
    }

    public function products()
    {

        $productTable = app(Product::class)->getTable();
        $categoryTable = app(Category::class)->getTable();

        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot([
                'excepted',
                'created_at',
                'updated_at',
                'qty',
                'sale',
                'price',
                'price_changed',
                'qty_changed',
            ])
            ->join("$categoryTable as sorting_table", "sorting_table.id", "$productTable.category_id")
            ->join("$categoryTable as sorting_parent_table", "sorting_parent_table.id", "sorting_table.parent_id")
            ->addSelect(DB::raw('sorting_parent_table.sorder * 100000 + sorting_table.sorder as sorder'))
            ->orderByRaw("sorting_parent_table.sorder * 100000 + sorting_table.sorder")
            ->select(["$productTable.*"]);
    }

    public function address()
    {
        return $this->belongsTo(ProfileAddress::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getUserCurrentOrders($id, $history = false)
    {
        return static::query()
            ->where('user_id', $id)
            ->where('status', $history ? '=' : '!=', 'Shipped')
            ->orderBy('created_at', 'DESC')
            ->with(['address', 'products'])
            ->paginate(15);
    }

    protected static function booted()
    {
        static::saving(function(self $order) {
            return $order->fill([
                'updated_status' => collect($order->getDirty())->has('status') ? $order->freshTimestamp() : $order->updated_status,
            ]);
        });
    }
}
