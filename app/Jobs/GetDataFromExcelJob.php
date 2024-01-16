<?php

namespace App\Jobs;

use App\Models\PreorderCategory;
use App\Models\PreorderProduct;
use App\Models\PreorderTableSheet;
use App\Services\PageParser\PageParserManager;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class GetDataFromExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $image;

    public function __construct(private PreorderTableSheet $preorderTableSheet)
    {
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function handle()
    {
        $emptyCategory = 'Без категории';
        $emptySubcategory = 'Без подкатегории';
        $preorder = $this->preorderTableSheet->preorder()->first();

        $markup = $this->preorderTableSheet->markup;
        //dd($this->preorderTableSheet->markup);

        $file = storage_path() . '/app/public/' . json_decode($preorder->file)[0]->download_link;

        $reader = IOFactory::createReaderForFile($file);

        $reader->setLoadSheetsOnly($this->preorderTableSheet->title);

        $spreadsheet = $reader->load($file);

        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;

        $htmlParser = (new PageParserManager())->getParser();

        $shouldParseMerchPrices = false;
        if ($preorder->merch_file) {
            $shouldParseMerchPrices = true;
            $merchFile = storage_path() . '/app/public/' . json_decode($preorder->merch_file)[0]->download_link;
            $merchReader = IOFactory::createReaderForFile($merchFile);
            $merchReader->setLoadSheetsOnly($this->preorderTableSheet->title);
            $merchSpreadsheet = $merchReader->load($merchFile);
            $merchSheet = $merchSpreadsheet->getActiveSheet();
        }

        while ($row <= $sheet->getHighestRow()) {
//            if ($row == 100){
//                break;
//            }

            $notExistBarcode = (is_null($sheet->getCell($markup->barcode . $row)->getValue())
                || (int)$sheet->getCell($markup->barcode . $row)->getValue() === 0);

            if (
                $notExistBarcode
                || (is_null($sheet->getCell($markup->title . $row)->getValue())
                    && is_null($sheet->getCell($markup->price . $row)->getValue()))
            ) {
                $row++;
                continue;
            }

            $barcode = $sheet->getCell($markup->barcode . $row)->getValue();

            $product = PreorderProduct::where('barcode', $barcode)->first();

            $sku = $preorder->id . $barcode;
            $categoryName = $sheet->getCell($markup->category . $row)->getValue();
            if (!$categoryName) {
                $categoryName = $emptyCategory;
            }
            $currentCategory = PreorderCategory::query()
                ->where('title', $categoryName)
                ->where('preorder_id', $preorder->id)
                ->first();

            if (is_null($currentCategory)) {
                $currentCategory = PreorderCategory::query()->create([
                    'title' => $categoryName,
                    'preorder_id' => $preorder->id,
                    'preorder_table_sheet_id' => $this->preorderTableSheet->id
                ]);
            }
            if(!is_null($markup->subcategory)) {
                $subCategoryName = $sheet->getCell($markup->subcategory . $row)->getValue();
            } else {
                $subCategoryName = $emptySubcategory;
            }

            $currentsubCategory = PreorderCategory::where('preorder_category_id', $currentCategory->id)
                ->where('title', $subCategoryName)
                ->first();
            if (!$currentsubCategory) {
                $currentsubCategory = PreorderCategory::create([
                    'title' => $subCategoryName,
                    'preorder_id' => $preorder->id,
                    'preorder_category_id' => $currentCategory->id,
                    'preorder_table_sheet_id' => $this->preorderTableSheet->id
                ]);
            }

//            $product = $currentCategory->products()->where('sku', '=', $sku)->first();

//            if ($product === null) {
            $description = $image = $url = null;

            if (!is_null($markup->description)) {
                $description = $sheet->getCell($markup->description . $row)->getValue();
            }

            if (!$product && !is_null($markup->image) && !is_null($sheet->getCell($markup->image . $row)->getValue())) {
                try {
                    if (empty($sheet->getCell($markup->image . $row)->getHyperlink()->getUrl())) {

                        $hyperlink = $sheet->getCell($markup->image . $row)->getValue();

                        $imageParamCellNumber = Str::between($hyperlink, '&', '&');

                        $imageParamCellName = $sheet->getCell($imageParamCellNumber)->getValue();

                        $res = str_replace('"&' . $imageParamCellNumber . '&"', $imageParamCellName, $hyperlink);

                        $image = rtrim(explode(',', Str::between($res, '"', '"'))[0], '"');
                    }

                    $this->image = !empty($image) ? $image : $sheet->getCell($markup->image . $row)->getHyperlink()->getUrl();

                } catch (\Exception $e) {
                    return null;
                }

                if (!empty($this->image)) {
                    $image = $this->downloadImage($this->image, $preorder, $sku);
                }
            }

            if (!is_null($markup->link)) {
                $url = $sheet->getCell($markup->link . $row)->getHyperlink()->getUrl();

                if (!empty($url)) {
                    try {
                        $html = $htmlParser->getHtml($url);

                        $image = $html->getImage();
                        $description = $html->getDescription();

                        if (!empty($image)) {
                            $image = $this->downloadImage($image, $preorder, $sku);
                        }
                    } catch (GuzzleException) {
                        $url = null;
                    }
                }
            }

            if ($product) {
                $product->update([
                    'preorder_id' => $preorder->id,
                    'preorder_category_id' => $currentsubCategory->id,
                    'sku' => $sku,
                    'price' => $markup->price != null
                        ? $sheet->getCell($markup->price . $row)->getValue()
                        : '',

                    'multiplicity' => $markup->multiplicity != null
                        ? preg_replace("/[^0-9]/", '', $sheet->getCell($markup->multiplicity . $row)->getValue())
                        : 1,

                    'multiplicity_tu' => $markup->multiplicity_tu != null
                        ? preg_replace("/[^0-9]/", '', $sheet->getCell($markup->multiplicity_tu . $row)->getValue())
                        : 1,
                    'cell_number' => $row,
                    'merch_price' => $shouldParseMerchPrices ?
                        $merchSheet->getCell($markup->price . $row)->getValue() :
                        null
                ]);
            } else {
                PreorderProduct::updateOrCreate(['sku' => $sku, 'preorder_id' => $preorder->id], [
                    'sku' => $sku,

                    'title' => $markup->title != null
                        ? ($sheet->getCell($markup->title . $row)->getValue() ?? '')
                        : '',

                    'barcode' => $markup->barcode != null
                        ? $sheet->getCell($markup->barcode . $row)->getValue()
                        : '',

                    'price' => $markup->price != null
                        ? $sheet->getCell($markup->price . $row)->getValue()
                        : '',

                    'multiplicity' => $markup->multiplicity != null
                        ? preg_replace("/[^0-9]/", '', $sheet->getCell($markup->multiplicity . $row)->getValue())
                        : 1,

                    'multiplicity_tu' => $markup->multiplicity_tu != null
                        ? preg_replace("/[^0-9]/", '', $sheet->getCell($markup->multiplicity_tu . $row)->getValue())
                        : 1,

                    'container' => $markup->container != null
                        ? $sheet->getCell($markup->container . $row)->getValue()
                        : '',

                    'country' => $markup->country != null
                        ? $sheet->getCell($markup->country . $row)->getValue()
                        : '',

                    'packaging' => $markup->packaging != null
                        ? $sheet->getCell($markup->packaging . $row)->getValue()
                        : '',

                    'package_type' => $markup->package_type != null
                        ? $sheet->getCell($markup->package_type . $row)->getValue()
                        : '',

                    'weight' => $markup->weight != null
                        ? $sheet->getCell($markup->weight . $row)->getValue()
                        : '',

                    'season' => $markup->season != null
                        ? $sheet->getCell($markup->season . $row)->getValue()
                        : '',

                    'r_i' => $markup->r_i != null
                        ? $sheet->getCell($markup->r_i . $row)->getValue()
                        : '',
                    'seasonality' => $markup->seasonality != null
                        ? $sheet->getCell($markup->seasonality . $row)->getValue()
                        : '',
                    'plant_height' => $markup->plant_height != null
                        ? $sheet->getCell($markup->plant_height . $row)->getValue()
                        : '',
                    'packaging_type' => $markup->packaging_type != null
                        ? $sheet->getCell($markup->packaging_type . $row)->getValue()
                        : '',
                    'package_amount' => $markup->package_amount != null
                        ? $sheet->getCell($markup->package_amount . $row)->getValue()
                        : '',
                    'culture_type' => $markup->culture_type != null
                        ? $sheet->getCell($markup->culture_type . $row)->getValue()
                        : '',
                    'frost_resistance' => $markup->frost_resistance != null
                        ? $sheet->getCell($markup->frost_resistance . $row)->getValue()
                        : '',
                    'additional_1' => $markup->additional_1 != null
                        ? $sheet->getCell($markup->additional_1 . $row)->getValue()
                        : '',
                    'additional_2' => $markup->additional_2 != null
                        ? $sheet->getCell($markup->additional_2 . $row)->getValue()
                        : '',
                    'additional_3' => $markup->additional_3 != null
                        ? $sheet->getCell($markup->additional_3 . $row)->getValue()
                        : '',
                    'additional_4' => $markup->additional_4 != null
                        ? $sheet->getCell($markup->additional_4 . $row)->getValue()
                        : '',
                    'soft_limit' => $markup->soft_limit != null
                        ? $sheet->getCell($markup->soft_limit . $row)->getValue()
                        : null,
                    'hard_limit' => $markup->hard_limit != null
                        ? $sheet->getCell($markup->hard_limit . $row)->getValue()
                        : null,
                    'image' => $image,
                    'description' => $description,
                    'preorder_category_id' => $currentsubCategory/*?*/ ->id, //?? $currentCategory->id,
                    'preorder_id' => $preorder->id,
                    'cell_number' => $row,
                    'merch_price' => $shouldParseMerchPrices ?
                        $merchSheet->getCell($markup->price . $row)->getValue() :
                        null
                ]);
            }
//            }

            $row++;
        }
    }

    private function downloadImage($link, $preorder, $sku): ?string
    {
        try {
            $link = !strpos('%20', $link) ? str_replace(' ', '%20', $link) : $link;

            $after = Str::after($link, 'https://');
            $domen = Str::before($after, '/');

            if (preg_match('/[А-Яа-яЁё]/u', $domen)) {

                $link = str_replace($domen, idn_to_ascii($domen), $link);
            }

            $image = file_get_contents($link);

            $imageName = $sku . '.jpg';

            if (!file_exists(storage_path() . '/app/public/preorder/' . $preorder->id)) {
                mkdir(storage_path() . '/app/public/preorder/' . $preorder->id, 0777, true);
            }

            $path = storage_path() . '/app/public/preorder/' . $preorder->id . '/' . $imageName;
            file_put_contents($path, $image);
            return 'preorder/' . $preorder->id . '/' . $imageName;
        } catch (\Exception $e) {
            return null;
        }
    }
}
