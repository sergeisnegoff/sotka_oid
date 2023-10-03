<?php

namespace App\Services\Preorder;

use App\Models\Preorder;
use App\Models\PreorderCategory;
use App\Models\PreorderCheckout;
use App\Models\PreorderProduct;
use App\Models\PreorderTableSheet;
use App\Models\User;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PreorderService
{
    public static function getCartKey($id = null): string
    {
        if (!$id) $id = auth()->id();
        return 'preorder_cart_' .  $id;
    }

    public static function getLatestPreorderKey($id = null): string
    {
        if (!$id) $id = auth()->id();
        return 'latest_preorder_' . $id;
    }

    public static function getFullCart(): array
    {
        return cache()->get(self::getCartKey(), []);
    }

    public static function getFullUserCart($user_id) {
        return cache()->get(self::getCartKey($user_id), []);
    }

    public static function getCart(): array
    {
        $cart = cache()->get(self::getCartKey(), []);

        if (empty($cart)) {
            return [];
        }

        if (request()->route()->getName() === 'preorder_category_page') {
            $preorder_id = request()->route()->parameter('id');
        } else {
            $preorder_id = self::getLatestPreorder();
        }

        if (! $preorder_id) {
            $preorder_id = $cart[0]['preorder_id'];
        }

        $cart = array_filter($cart, function ($item) use ($preorder_id) {
            return $item['preorder_id'] === (int)$preorder_id;
        });

        return $cart;
    }

    public static function getLatestPreorder(): ?int
    {
        return cache()->get(self::getLatestPreorderKey());
    }

    public static function setLatestPreorder(int $id): void
    {
        cache()->put(self::getLatestPreorderKey(), $id);
    }
    public static function setUserLatestPreorder(int $id, int $user_id): void
    {
        cache()->put(self::getLatestPreorderKey($user_id), $id);
    }

    public static function setLatestUserPreorder(int $id, int $user_id): void {
        cache()->put(self::getLatestPreorderKey($user_id), $id);
    }

    public static function updateCart(array $cart): void
    {
        cache()->put(self::getCartKey(), $cart);
    }
    public static function updateUserCart(array $cart, int $user_id) {
        cache()->put(self::getCartKey($user_id), $cart);
    }

    public static function removeFromCart(int $id): void
    {
        $cart = self::getFullCart();
        unset($cart[$id]);
        cache()->put(self::getCartKey(), $cart);
    }

    public static function removePreorderFromCart(int $id, int $user_id = null): void
    {
        if (!$user_id) $user_id = auth()->user()->id;
        $cart = self::getFullUserCart($user_id);

        $cart = array_filter($cart, function ($item) use ($id) {
            return $item['preorder_id'] !== $id;
        });

        cache()->put(self::getCartKey($user_id), $cart);
    }

    public static function getActivePreorders() {
        return Preorder::whereDay('end_date', '>', Carbon::now());
    }

    public static function createSummary(Preorder $preorder) {
        Debugbar::disable();
        $sheets = PreorderTableSheet::where('preorder_id', $preorder->id)->where('active', true)->get();

        $outSpreadsheet = new Spreadsheet();
        $outSpreadsheet->removeSheetByIndex(0);
        foreach ($sheets as $sheet) {
            $worksheet = new Worksheet($outSpreadsheet, $sheet->title);
            $outSpreadsheet->addSheet($worksheet);
            $worksheet->setCellValue('A4', 'Наименование товара');
            $worksheet->setCellValue('B4', 'Штрихкод');
            $worksheet->setCellValue('C4', 'Цена');
            $worksheet->setCellValue('E3', 'Менеджер');

            $sheetCategories = PreorderCategory::where('preorder_table_sheet_id', $sheet->id)->get();
            $row = 5;
            $productRows = [];
            //Пишем продукты в таблицу
            foreach ($sheetCategories as $sheetCategory) {
                $worksheet->setCellValue('A'.$row, $sheetCategory->title);
                $cellStyle = $worksheet->getStyle('A'.$row.':AZ'.$row);
                $cellStyle->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('62b0df');
                $row++;
                foreach ($sheetCategory->products as $product) {
                    $worksheet->setCellValue('A'.$row, $product->title);
                    $worksheet->setCellValueExplicit('B'.$row, $product->barcode, DataType::TYPE_STRING);
                    $worksheet->setCellValue('C'.$row, $product->price);
                    $productRows[$product->barcode] = $row;
                    $row++;
                }
            }
            //Теперь получаем инфо о заказах
            $productSummaries = [];
            $userColIterator = new ColumnIterator('F');

            $preorderCheckouts = PreorderCheckout::where('preorder_id', $preorder->id)->get();
            foreach ($preorderCheckouts as $checkout) {
                $worksheet->setCellValue($userColIterator->getCurrent().'1', $checkout->created_at);
                $worksheet->setCellValue($userColIterator->getCurrent().'2', $checkout->user->name);
                $worksheet->getStyle($userColIterator->getCurrent().'1')->getAlignment()->setTextRotation(90);
                $worksheet->getStyle($userColIterator->getCurrent().'2')->getAlignment()->setTextRotation(90);
                $managerTable = $checkout->user->manager_table;
                $managerName = '-';
                if ($managerTable) {
                    $manRecord = \DB::table($managerTable)->where('id', $checkout->user->manager_id)->first();
                    if ($manRecord) {
                        $managerName = $manRecord->name;
                    }
                }
                $worksheet->setCellValue($userColIterator->getCurrent().'3', $managerName);

                foreach ($checkout->products as $product) {
                    if (!isset($productRows[$product->preorder_product->barcode])) continue;
                    if (!isset($productSummaries[$product->preorder_product->barcode])) $productSummaries[$product->preorder_product->barcode] = ['qty' => 0, 'summary' => 0];
                    $productSummaries[$product->preorder_product->barcode]['qty'] += $product->qty;
                    $productSummaries[$product->preorder_product->barcode]['summary'] += $product->total();
                    $worksheet->setCellValue($userColIterator->getCurrent().$productRows[$product->preorder_product->barcode], $product->qty);
                }
                $userColIterator->setNext();
            }
            $worksheet->setCellValue('E1', 'Дата заказа');
            $worksheet->setCellValue('E2', 'Пользователь');
            $worksheet->setCellValue('D4', 'Заказ общий');
            $worksheet->setCellValue('E4', 'Сумма заказа');
            foreach ($productRows as $barcode => $row) {
                if (!isset($productSummaries[$barcode])) continue;
                $worksheet->setCellValue('D'.$row, $productSummaries[$barcode]['qty']);
                $worksheet->setCellValue('E'.$row, $productSummaries[$barcode]['summary']);
            }

            foreach($userColIterator->getRange() as $col) {
                $worksheet->getColumnDimension($col)->setAutoSize(true);
            }


        }

        $writer = IOFactory::createWriter($outSpreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $preorder->id . '.xlsx"');
        $writer->save('php://output');
    }

}
