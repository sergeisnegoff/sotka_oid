<?php

namespace App\Jobs;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcessUpdateJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $items;
    public $category;
    public $main_category;

    public function __construct(string $main_category, string $category, array $items) {
        $this->main_category = $main_category;
        $this->category = $category;
        $this->items = $items;
    }

    public function handle() {
        /** @var Category $category */
        $category = Category::query()->firstOrCreate(['title' => $this->main_category, 'parent_id' => 0]);
        /** @var Category $subcategory */
        $subcategory = Category::query()->firstOrCreate(['title' => $this->category, 'parent_id' => $category->id]);

        foreach ($this->items as $item) {
            $product = Product::query()->updateOrCreate(
                [
                    'oneC_7' => $item['xml_id']
                ], [
                    'title' => $item['name'],
                    'category_id' => $subcategory->id,
                    'price' => $item['cost'],
                    'total' => $item['total'],
                    'multiplicity' => $item['multiplicity'],
                ]
            );

            if (!is_null($image = $item['image'])) {
                $ext = pathinfo($image, PATHINFO_EXTENSION);
                if (in_array($ext, getImageExtensions()) &&
                    checkSrc($image) === true &&
                    ($content = @file_get_contents($image)) &&
                    empty($product->images)
                ) {
                    $folder = date('FY');
                    $fileName = md5(rand(1, 999999)) . '.' . $ext;

                    Storage::put('public/products/' . $folder . '/' . $fileName, $content);

                    $product->images = !empty($fileName) ? 'products/' . $folder . '/' . $fileName : '';
                }
            }

            if (!is_null($manufacturer = $item['manufacturer'])) {
                $brand = Brands::query()->firstOrCreate(['title' => $item['manufacturer']]);

                $subfilter = DB::table('subfilters')->where('title', $manufacturer)->first();

                if (is_null($subfilter)) {
                    $subfilter = DB::table('subfilters')->insertGetId(['title' => $manufacturer, 'filter_id' => 2]);
                }

                $subspecification = DB::table('subspecifications')->where('title', $manufacturer)->first();
                if (is_null($subspecification)) {
                    $subspecification = DB::table('subspecifications')->insertGetId(
                        ['title' => $manufacturer, 'specification' => 6]
                    );
                }

                if (!DB::table('products_pivot_subfilter')
                    ->where(
                        [
                            'product_id' => $product->id,
                            'subfilter_id' => is_object($subfilter) ? $subfilter->id : $subfilter
                        ]
                    )
                    ->exists()) {
                    DB::table('products_pivot_subfilter')->insert(
                        [
                            'product_id' => $product->id,
                            'subfilter_id' => is_object($subfilter) ? $subfilter->id : $subfilter
                        ]
                    );
                }

                if (!DB::table('products_pivot_specifications')->where(
                    [
                        'product_id' => $product->id,
                        'subspecification_id' => is_object($subspecification) ? $subspecification->id
                            : $subspecification
                    ]
                )->exists()) {
                    DB::table('products_pivot_specifications')->insert(
                        [
                            'product_id' => $product->id,
                            'subspecification_id' => is_object($subspecification) ? $subspecification->id
                                : $subspecification
                        ]
                    );
                }

                $product->brand_id = is_object($brand) ? $brand->id : 0;
            }

            if (!is_null($filters = $item['filters'])) {

                $subfilter = DB::table('subfilters')->where('title', $filters)->first();
                if (is_null($subfilter)) {
                    $subfilter = DB::table('subfilters')->insertGetId(['title' => $filters, 'filter_id' => 3]);
                }

                $subspecification = DB::table('subspecifications')->where('title', $filters)->first();
                if (is_null($subspecification)) {
                    $subspecification = DB::table('subspecifications')->insertGetId(
                        [
                            'title' => $filters,
                            'specification' => 7
                        ]
                    );
                }

                if (!DB::table('products_pivot_subfilter')->where(
                    [
                        'product_id' => $product->id,
                        'subfilter_id' => is_object($subfilter) ? $subfilter->id : $subfilter
                    ]
                )->exists()) {
                    DB::table('products_pivot_subfilter')->insert(
                        [
                            'product_id' => $product->id,
                            'subfilter_id' => is_object($subfilter) ? $subfilter->id : $subfilter
                        ]
                    );
                }

                if (!DB::table('products_pivot_specifications')->where(
                    [
                        'product_id' => $product->id,
                        'subspecification_id' => is_object($subspecification) ? $subspecification->id
                            : $subspecification
                    ]
                )->exists()) {
                    DB::table('products_pivot_specifications')->insert(
                        [
                            'product_id' => $product->id,
                            'subspecification_id' => is_object($subspecification) ? $subspecification->id
                                : $subspecification
                        ]
                    );
                }
            }

        }
    }
}
