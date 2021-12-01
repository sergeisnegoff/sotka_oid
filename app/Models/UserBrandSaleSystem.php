<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserBrandSaleSystem extends Model
{
    protected $table = 'salesystem_users_brands';
    use HasFactory;

    protected $fillable = ['brand_id', 'amount', 'sale', 'user_id'];

    public static function checkedBrands($id) {
        return DB::table('user_brand_sales')->where('user_id', $id)->select('brand_id')->groupBy('brand_id')->get()->pluck('brand_id');
    }

    public static function getBrandSale($brand_id, $id) {
        return DB::table('user_brand_sales')->select('sale')->where('brand_id', $brand_id)->where('user_id', $id)->first();
    }
}
