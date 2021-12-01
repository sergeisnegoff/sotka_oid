<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Category extends Model
{
    protected $fillable = ['title', 'parent_id', 'sorder'];
    public function product()
    {
        return $this->hasMany('App\Product','category_id');
    }
    public static function addSaleToCategory($category_id, $amount, $sale) {
        return DB::table('categories_sales')->insert(['category_id' => $category_id, 'amount' => $amount, 'sale' => $sale]);
    }

    public static function removeCategorySales() {
        DB::table('categories_sales')->truncate();
    }

    public function newQuery()
    {
        $query = parent::newQuery();

        $query->orderBy('sorder', 'asc');

        return $query;
    }
}
