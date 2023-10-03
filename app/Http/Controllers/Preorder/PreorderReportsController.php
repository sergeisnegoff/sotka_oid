<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\Preorder;
use App\Models\PreorderProduct;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class PreorderReportsController extends Controller
{
    public function index()
    {
        $product = PreorderProduct::findOrFail(1);

        $preorderProducts = Preorder::findOrFail($product->preorder_id)->products;

        $inputFileName = public_path('blank.xls');

        $spreadsheet = IOFactory::load($inputFileName);

        // Set cell A1 with a string value
        foreach ($preorderProducts as $product) {
            $spreadsheet->getActiveSheet()->setCellValue('A' . $product->cell_number, $product->title);
            $spreadsheet->getActiveSheet()->setCellValue('b' . $product->cell_number, $product->barcode);
            $spreadsheet->getActiveSheet()->setCellValue('c' . $product->cell_number, $product->price);
        }

        // Write an .xlsx file
        $writer = new Xls($spreadsheet);

// Save .xlsx file to the current directory
        $writer->save('done.xls');
    }
}
