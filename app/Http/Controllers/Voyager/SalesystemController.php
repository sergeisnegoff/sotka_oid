<?php

namespace App\Http\Controllers\Voyager;

use App\Category;
use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\BrandSalesModel;
use App\Models\UserBrandSaleSystem;
use App\Salesystem;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;

class SalesystemController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    public function updateTable() {
        $data = \Illuminate\Support\Facades\Request::post('priceRange');
        $brandsCheckBox = \Illuminate\Support\Facades\Request::post('brands');

        UserBrandSaleSystem::truncate();
        (new \App\Models\Brands)->removeBrandSales();

        foreach ($data as $price => $brands) {
            foreach ($brands as $brand_id => $percent) {
                if (in_array($brand_id, array_keys($brandsCheckBox)))
                    Brands::addSaleToBrand($brand_id, $price, $percent);

                if (!empty($percent))
                    BrandSalesModel::create(['sale' => (float)$percent, 'amount' => $price, 'brand_id' => $brand_id]);
            }
        }

        return Redirect::route('voyager.brand-sales.index');
    }
}
