<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Salesystem extends Model
{
    protected $fillable = ['percent', 'amount', 'type', 'category_id'];

    public static function checkedCategories() {
        return DB::table('categories_sales')->select('category_id')->groupBy('category_id')->get()->pluck('category_id');
    }

    public static function getCategorySale($category_id, $amount) {
        return DB::table('categories_sales')->where('category_id', $category_id)->where('amount', '<=', $amount)->orderBy('amount', 'ASC')->first();
    }
}
