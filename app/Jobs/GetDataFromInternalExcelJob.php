<?php

namespace App\Jobs;

use App\Category;
use App\Http\Controllers\Voyager\CategoriesController;
use App\Imports\ProductUpdateImport;
use App\Models\PreorderCategory;
use App\Models\PreorderProduct;
use App\Models\PreorderTableSheet;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GetDataFromInternalExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private PreorderTableSheet $preorderTableSheet;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PreorderTableSheet  $preorderTableSheet)
    {
        $this->preorderTableSheet = $preorderTableSheet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $preorder = $this->preorderTableSheet->preorder;
        //dd($preorder);
        $markup = $this->preorderTableSheet->markup;

        $file = storage_path().'/app/public/'.json_decode($preorder->file)[count(json_decode($preorder->file)) - 1]->download_link;

        $reader = IOFactory::createReaderForFile($file);

        $reader->setLoadSheetsOnly($this->preorderTableSheet->title);
        $spreadsheet = $reader->load($file);
        $shouldParseMerchPrices = false;
        if ($preorder->merch_file) {
            $shouldParseMerchPrices = true;
            $merchFile = storage_path() . '/app/public/' . json_decode($preorder->merch_file)[0]->download_link;
            $merchReader = IOFactory::createReaderForFile($merchFile);
            $merchReader->setLoadSheetsOnly($this->preorderTableSheet->title);
            $merchSpreadsheet = $merchReader->load($merchFile);
            $merchSheet = $merchSpreadsheet->getActiveSheet();
        }
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;
        $notFoundProductBarcodes = [];
        $emptyBarcodeRows = [];

        while ($row <= $sheet->getHighestRow()) {
            $barcode = $sheet->getCell("A$row")->getValue();
            //dd($markup);
            if (is_null($barcode)) {
                $emptyBarcodeRows[] = $row;
                $row++;
                continue;
            }
            $barcode = str_replace(' ', '', $barcode);
            if (!$barcode) {
                $emptyBarcodeRows[] = $row;
                $row++;
                continue;
            }
            $product = Product::where('barcode', $barcode)->first();
            if (!$product) {
                $notFoundProductBarcodes[] = [
                        'row' => $row,
                        'barcode' => $barcode,
                    ];
                $row++;
                continue;
            }
            //dd($product);
            $productCategory = $product->category;
            $rootProductCategory = Category::find($productCategory->parent_id);
//            if (!$rootProductCategory) {
//                dd('Проблема с товаром в строке ' . $row, $product, $productCategory);
//            }

            $currentCategory = PreorderCategory::firstOrCreate(
                ['title' => $rootProductCategory->title, 'preorder_id' => $preorder->id],
                [
                   'title' => $rootProductCategory->title,
                   'preorder_id' => $preorder->id,
                   'preorder_category_id' => null,
                   'preorder_table_sheet_id' => $this->preorderTableSheet->id
                ]);
            $currentSubCategory = PreorderCategory::firstOrCreate([
                    'title' => $productCategory->title,
                    'preorder_id' => $preorder->id,
                    'preorder_category_id' => $currentCategory->id
                    ],
                [
                    'title' => $productCategory->title,
                    'preorder_id' => $preorder->id,
                    'preorder_category_id' => $currentCategory->id,
                    'preorder_table_sheet_id' => $this->preorderTableSheet->id
                ]);

            $image = null;
            $soft_limit = null;
            $hard_limit = null;
            if ($sl = $sheet->getCell($markup->soft_limit.$row)->getValue())
                $soft_limit = $sl;
            if ($hl = $sheet->getCell($markup->hard_limit.$row)->getValue())
                $hard_limit = $hl;
            if ($product->images) {
//                try {
//                    $currentImagePath = storage_path('app/public/' . $product->images);
//                    $newImagePath = storage_path('app/public/preorder/' . $preorder->id . '/' . basename($product->images));
//                    if (\Storage::exists($currentImagePath))
//                        if (\Storage::copy($currentImagePath, $newImagePath))
//                            $image = $newImagePath;
//                } catch (\Exception $e) {
//                    \Log::log(LOG_WARNING, $e->getMessage());
//                }
                $image = $product->images;
            }

            $price = $sheet->getCell($markup->price.$row)->getValue() ?? $product->price;
            $price = trim($price);
            $price = trim($price, '=');


            $multiplicity = $sheet->getCell($markup->multiplicity.$row)->getValue() ?? $product->multiplicity;
            //$multiplicity = $product->multiplicity;

            Log::channel('import')->info('Row #: ' . $product);
            try {
                $preorderProduct = PreorderProduct::updateOrCreate([
                    'title' => $product->title,
                    'preorder_category_id' => $currentSubCategory->id,
                    'preorder_id' => $preorder->id
                ], [
                    'preorder_id' => $preorder->id,
                    'title' => $product->title,
                    'barcode' => $product->barcode,
                    'multiplicity' => $multiplicity,
                    'description' => $product->description,
                    'image' => $image,
                    'price' => $price,
                    'merch_price' => $price,
                    'preorder_category_id' => $currentSubCategory->id,
                    'cell_number' => $row,
                    'soft_limit' => $soft_limit,
                    'hard_limit' => $hard_limit
                ]);
            } catch (\Exception $exception) {
                Log::channel('import')->info('Error at row #: ' . $row . '. message ' . $exception->getMessage());
            }
            $row++;
        }



        if (count($emptyBarcodeRows) || count($notFoundProductBarcodes)){

            $updatePreorder = cache('update_preorder');
            if (empty($updatePreorder)) {
                if (count($emptyBarcodeRows) > 0) $updatePreorder['no_barcode_rows'] = $emptyBarcodeRows;
                if (count($notFoundProductBarcodes) > 0) $updatePreorder['no_products_rows'] = $notFoundProductBarcodes;
            }
            else {
                if (count($emptyBarcodeRows) > 0) {
                    if (array_key_exists('no_barcode_rows', $updatePreorder)) {
                        $updatePreorder['no_barcode_rows'] = array_merge($updatePreorder['no_barcode_rows'], $emptyBarcodeRows);
                    }
                    else {
                        $updatePreorder['no_barcode_rows'] = $emptyBarcodeRows;
                    }
                }

                if (count($notFoundProductBarcodes) > 0) {
                    if (array_key_exists('no_products_rows', $updatePreorder)) {
                        $updatePreorder['no_products_rows'] = array_merge($updatePreorder['no_products_rows'], $notFoundProductBarcodes);
                    }
                    else {
                        if (count($notFoundProductBarcodes) > 0) $updatePreorder['no_products_rows'] = $notFoundProductBarcodes;
                    }
                }
            }
            cache(['update_preorder' => $updatePreorder]);

            SendImportReport::dispatch();
        }
    }
}
