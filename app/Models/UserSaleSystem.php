<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserSaleSystem extends Model
{
    protected $table = 'salesystem_users';
    use HasFactory;

    protected $fillable = ['sale', 'amount', 'category_id', 'user_id'];

    public static function checkedCategories($id) {
        return DB::table('user_sales')->where('user_id', $id)->select('category_id')->groupBy('category_id')->get()->pluck('category_id');
    }

    public static function getCategorySale($category_id, $id) {
        return DB::table('user_sales')->where('category_id', $category_id)->where('user_id', $id)->first();
    }
}
