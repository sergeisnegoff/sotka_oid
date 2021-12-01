<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Brands extends Model
{
    use HasFactory;
    protected $fillable = ['title'];
    public function removeBrandSales() {
        DB::table('brand_sales')->truncate();
    }

    public static function addSaleToBrand($brand_id, $amount, $sale) {
        return DB::table('brand_sales')->insert(['brand_id' => $brand_id, 'amount' => $amount, 'sale' => $sale]);
    }

    public function removeBrandSalesToUser($id) {
        DB::table('user_brand_sales')->where('user_id', $id)->delete();
    }
}
