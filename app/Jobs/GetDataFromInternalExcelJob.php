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
        $markup = $this->preorderTableSheet->markup;

        $file = storage_path().'/app/public/'.json_decode($preorder->file)[0]->download_link;

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

        while ($row < $sheet->getHighestRow()) {
            if (is_null($sheet->getCell("A$row")->getValue()))
                continue;
            $barcode = $sheet->getCell("A$row")->getValue();
            $product = Product::where('barcode', $barcode)->first();
            if (!$product)
                continue;
            $productCategory = $product->category;
            $rootProductCategory = Category::find($productCategory->parent_id);
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
                try {
                    $currentImagePath = storage_path('app/public/' . $product->image);
                    $newImagePath = storage_path('app/public/preorder/' . $preorder->id . '/' . basename($product->image));
                    if (\Storage::exists($currentImagePath))
                        if (\Storage::copy($currentImagePath, $newImagePath))
                            $image = $newImagePath;
                } catch (\Exception $e) {
                    \Log::log(LOG_WARNING, $e->getMessage());
                }
            }
            $preorderProduct = PreorderProduct::updateOrCreate([
                'title' => $product->title,
                'preorder_category_id' => $currentSubCategory->id,
                'preorder_id' => $preorder->id
            ], [
                'preorder_id' => $preorder->id,
                'title' => $product->title,
                'barcode' => $product->barcode,
                'multiplicity' => $product->multiplicity,
                'description' => $product->description,
                'image' => $image,
                'price' => $product->price,
                'preorder_category_id' => $currentSubCategory->id,
                'cell_number' => $row,
                'soft_limit' => $soft_limit,
                'hard_limit' => $hard_limit
            ]);
            $row++;
        }
    }
}
