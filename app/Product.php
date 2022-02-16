<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


/**
 * @method static Builder|static multiplicity()
 */
class Product extends Model
{
    protected $fillable = [
        'title',
        'description',
        'images',
        'category_id',
        'brand_id',
        'oneC_7',
        'oneC_8',
        'price',
        'qty',
        'video_link',
        'multiplicity',
        'total',
        'main_page'
    ];


    public function scopeMultiplicity($query) {
        return $query->where('multiplicity', '!=', 0);
    }


    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id')->orderBy('sorder', 'ASC');
    }

    public function subSpecification()
    {
        return $this->belongsToMany('App\Subspecification', 'products_pivot_specifications');
    }

    public function subFilter()
    {
        return $this->belongsToMany('App\Subfilter', 'products_pivot_subfilter');
    }

    public function mainPageCategory()
    {
        $items = DB::table('categories')->where('main_page', 1)->first();

        if (!empty($items))
            if ($items->parent_id == 0)
                return DB::table('categories')->where('parent_id', $items->id)->get()->pluck('id');
            else
                return [$items->id];
        else
            return null;
    }

    public function mainPageBrand() {
        return DB::table('brands')->where('main_page', 1)->first();
    }


    public function scopeFilter($builder, $filters)
    {
        return $filters->apply($builder);
    }

    public function countProductsByCategoryID($id)
    {
        return Product::query()->withCount('category')->where('total', '!=', 0)->where('multiplicity', '!=', 0)->where('category_id', $id)->count();
    }

    public static function getProductCategory($id)
    {
        return Product::query()->select('category_id')->where('id', $id)->first()->category_id;
    }

    public static function getBrandID($id)
    {
        return Product::query()->select('brand_id')->where('id', $id)->first()->brand_id;
    }

    public static function getSaleForProduct($id)
    {
        return Product::query()->select('sale')->where('id', $id)->first()->sale;
    }

    public static function getMaxSaleToProduct($product_id, $amount, $qty)
    {
        /*$totalAmount = 0;
        foreach (session()->get('cart') as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        $minAmountForSale = DB::table('salesystems')->select('amount')->orderBy('amount', 'ASC')->limit(1)->first();

        $product = \App\Product::getSaleForProduct($product_id);
        $category = Category::find(\App\Product::getProductCategory($product_id));

        if ($minAmountForSale->amount <= $totalAmount)
            $categoryVolume = \App\Salesystem::getCategorySale(\App\Product::getProductCategory($product_id), $amount * $qty);

        $brand = BrandSalesModel::getBrandSale(\App\Product::getBrandID($product_id), $amount * $qty);

        $userCategory = UserSaleSystem::getCategorySale(\App\Product::getProductCategory($product_id), Auth::user()->id);
        $userBrand = UserBrandSaleSystem::getBrandSale(\App\Product::getBrandID($product_id), Auth::user()->id);

        $cmp['product'] = $product ?: 0;
        $cmp['category'] = $category ? $category->sale : 0;

        if ($minAmountForSale->amount <= $totalAmount)
            $cmp['categoryVolume'] = $categoryVolume ? $categoryVolume->sale : 0;

        $cmp['brand'] = $brand ? $brand->sale : 0;
        $cmp['userCategory'] = $userCategory ? $userCategory->sale : 0;
        $cmp['userBrand'] = $userBrand ? $userBrand->sale : 0;


        $max = 0;
        foreach ($cmp as $sale) {
            if ($sale > $max)
                $max = $sale;
        }*/

//        $max = Auth::user()->personal_sale;

//        return $max;
    }
    public static function deleteSpecifications($id) {
        return DB::table('products_pivot_specifications')->where(['product_id' => $id])->delete();
    }
    public static function addSpecification($id, $spec_id) {
        return DB::table('products_pivot_specifications')->insert(['product_id' => $id, 'subspecification_id' => $spec_id]);
    }

    public static function addSimilarProduct($id, $similar_id) {
        return DB::table('products_similar')->insert(['product_id' => $id, 'similar_id' => $similar_id]);
    }

    public static function getSimilarProducts($id) {
        return DB::table('products_similar')->where('product_id', $id)->get()->pluck('similar_id');
    }

    public static function deleteSimilarProducts($id) {
        return DB::table('products_similar')->where(['product_id' => $id])->delete();
    }
}


