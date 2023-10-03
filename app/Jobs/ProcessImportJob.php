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
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filename;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $filename){
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $spreadsheet = IOFactory::load($this->filename);

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $subcategory = 0;
        $category = new \stdClass();

        $ids = [];

        foreach ($sheetData as $key => $row) {
            if ($key >= 6) {
                if (!empty($row['H'])) {
                    $ids[] = trim($row['H']);
                    if (is_object($subcategory)) {
                        $product = Product::updateOrCreate(['oneC_7' => trim($row['H'])], [
                            'title' => $row['A'],
                            'category_id' => $subcategory->id,
                            'price' => $row['C'],
                            'total' => $row['F'],
                            'multiplicity' => $row['G']
                        ]);

                        if (!empty($row['J'])) {
                            $ext = pathinfo($row['J'], PATHINFO_EXTENSION);
                            if (in_array($ext, getImageExtensions())) {
                                if (checkSrc($row['J']) === true) {
                                    $content = @file_get_contents($row['J']);
                                    if ($content && empty($product->images)) {
                                        $folder = date('FY');
                                        $fileName = md5(rand(1, 999999)) . '.' . $ext;
                                        Storage::put('public/products/' . $folder . '/' . $fileName, $content);

                                        $product->images = !empty($fileName) ? 'products/' . $folder . '/' . $fileName : '';
                                    }
                                }
                            }
                        }


                        if (!empty($row['I'])) {
                            $brand = Brands::firstOrCreate(['title' => $row['I']], ['title' => $row['I']]);
                            //2
                            $subfilter = DB::table('subfilters')->where('title', $row['I'])->first();
                            if (is_null($subfilter))
                                $subfilter = DB::table('subfilters')->insertGetId(['title' => $row['I'], 'filter_id' => 2]);

                            $subspecification = DB::table('subspecifications')->where('title', $row['I'])->first();
                            if (is_null($subspecification))
                                $subspecification = DB::table('subspecifications')->insertGetId(['title' => $row['I'], 'specification' => 6]);

                            if (DB::table('products_pivot_subfilter')->where(['product_id' => $product->id, 'subfilter_id' => is_object($subfilter) ? $subfilter->id : $subfilter])->count() == 0)
                                DB::table('products_pivot_subfilter')->insert([
                                    'product_id' => $product->id,
                                    'subfilter_id' => is_object($subfilter) ? $subfilter->id : $subfilter
                                ]);

                            if (DB::table('products_pivot_specifications')->where(['product_id' => $product->id, 'subspecification_id' => is_object($subspecification) ? $subspecification->id : $subspecification])->count() == 0)
                                DB::table('products_pivot_specifications')->insert([
                                    'product_id' => $product->id,
                                    'subspecification_id' => is_object($subspecification) ? $subspecification->id : $subspecification
                                ]);

                            $product->brand_id = is_object($brand) ? $brand->id : 0;
                        }

                        if (!empty($row['K'])) {

                            $subfilter = DB::table('subfilters')->where('title', $row['K'])->first();
                            if (is_null($subfilter))
                                $subfilter = DB::table('subfilters')->insertGetId(['title' => $row['K'], 'filter_id' => 3]);

                            $subspecification = DB::table('subspecifications')->where('title', $row['K'])->first();
                            if (is_null($subspecification))
                                $subspecification = DB::table('subspecifications')->insertGetId(['title' => $row['K'], 'specification' => 7]);

                            if (DB::table('products_pivot_subfilter')->where(['product_id' => $product->id, 'subfilter_id' => is_object($subfilter) ? $subfilter->id : $subfilter])->count() == 0)
                                DB::table('products_pivot_subfilter')->insert([
                                    'product_id' => $product->id,
                                    'subfilter_id' => is_object($subfilter) ? $subfilter->id : $subfilter
                                ]);

                            if (DB::table('products_pivot_specifications')->where(['product_id' => $product->id, 'subspecification_id' => is_object($subspecification) ? $subspecification->id : $subspecification])->count() == 0)
                                DB::table('products_pivot_specifications')->insert([
                                    'product_id' => $product->id,
                                    'subspecification_id' => is_object($subspecification) ? $subspecification->id : $subspecification
                                ]);
                        }

                        $product->save();
                    }
                } else {
                    try {
                        $color = $spreadsheet->getSheet(0)->getStyle('A' . $key)->getFill()->getStartColor()->getRGB();
                        if ($color == '333333') {
                            $category = Category::firstOrCreate(['title' => $row['A'], 'parent_id' => 0], ['title' => $row['A'], 'parent_id' => 0]);
                        } elseif ($color == '808080') {
                            if (is_object($category))
                                $subcategory = Category::firstOrCreate(['title' => $row['A'], 'parent_id' => $category->id], ['title' => $row['A'], 'parent_id' => $category->id]);
                        }
                    } catch (Exception $e) {
                        dump($e->getMessage());
                    }
                }
            }
        }

        Product::query()->whereNotIn('oneC_7', $ids)->update([
            'total' => 0,
        ]);
    }
}
