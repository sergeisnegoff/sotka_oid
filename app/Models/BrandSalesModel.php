<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BrandSalesModel extends Model
{
    protected $table = 'salesystem_brands';
    use HasFactory;
    protected $fillable = ['sale', 'amount', 'brand_id'];

    public static function checkedBrands() {
        return DB::table('brand_sales')->select('brand_id')->groupBy('brand_id')->get()->pluck('brand_id');
    }

    public static function getBrandSale($brand_id, $amount) {
        return DB::table('brands')->select('sale')->where('id', $brand_id)->first();
    }
}
