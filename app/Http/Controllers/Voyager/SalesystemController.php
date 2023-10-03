<?php

namespace App\Http\Controllers\Voyager;

use App\Models\Brands;
use App\Models\BrandSalesModel;
use App\Models\UserBrandSaleSystem;
use Illuminate\Support\Facades\Redirect;

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
