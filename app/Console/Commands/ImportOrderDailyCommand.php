<?php

namespace App\Console\Commands;

use App\Mail\OrdersMail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportOrderDailyCommand extends Command
{
    protected $signature = 'export:orders {time}';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        $betweenTime = match ($this->argument('time')) {
            "8" => [Carbon::now()->subDay()->setTimeFromTimeString(setting('admin.THIRD_EXPORT')), Carbon::now()->setTimeFromTimeString(setting('admin.FIRST_EXPORT'))],
            "12" => [Carbon::now()->setTimeFromTimeString(setting('admin.FIRST_EXPORT')), Carbon::now()->setTimeFromTimeString(setting('admin.SECOND_EXPORT'))],
            "16" => [Carbon::now()->setTimeFromTimeString(setting('admin.SECOND_EXPORT')), Carbon::now()->setTimeFromTimeString(setting('admin.THIRD_EXPORT'))],
            default => [Carbon::today(), Carbon::tomorrow()],
        };

        $orders = Order::with('products', 'products', 'user', 'address')
            ->whereBetween('created_at', $betweenTime)
            ->latest()
            ->get();

        $spreadsheet = new Spreadsheet();

        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->setCellValue('A1', 'Номер заказа');
        $worksheet->setCellValue('B1', 'ФИО заказчика');
        $worksheet->setCellValue('C1', 'Адрес');
        $worksheet->setCellValue('D1', 'Комментарий');
        $worksheet->setCellValue('E1', 'Дата заказа');
        $worksheet->setCellValue('F1', 'Статус');

        $currentRow = 3;

        foreach ($orders as $order) {
            $address = $order->address->region.', '.$order->address->city.', '.$order->address->address;

            $worksheet->setCellValue('A'.$currentRow, $order->id);
            $worksheet->setCellValue('B'.$currentRow, $order->user->name);
            $worksheet->setCellValue('C'.$currentRow, $address);
            $worksheet->setCellValue('D'.$currentRow, $order->comment);
            $worksheet->setCellValue('E'.$currentRow, $order->created_at->format('d.m.Y H:i:s'));
            $worksheet->setCellValue('F'.$currentRow, $order->status);


            $worksheet->setCellValue('B'.$currentRow + 2, 'Название товара');
            $worksheet->setCellValue('C'.$currentRow + 2, 'Цена');
            $worksheet->setCellValue('D'.$currentRow + 2, 'Кол-во');
            $worksheet->setCellValue('E'.$currentRow + 2, 'Общая стоимость');

            $productRow = $currentRow + 4;

            foreach ($order->products as $product) {
                $worksheet->setCellValue('B'.$productRow, $product->title);
                $worksheet->setCellValue('C'.$productRow, $product->price);
                $worksheet->setCellValue('D'.$productRow, $product->pivot->qty);
                $worksheet->setCellValue('E'.$productRow, $product->pivot->price);

                $productRow++;
            }

            $currentRow += $order->products->count() !== 0 ? $order->products->count() + 5 : 4;
        }

        if (Storage::exists('public/excel/orders/') === false) {
            mkdir(storage_path('app/public') . '/excel/orders', 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/public').'/excel/orders/order-'.\Carbon\Carbon::now()->format('d-m-Y-H').'.xlsx');


        $file = storage_path('app/public').'/excel/orders/order-'.\Carbon\Carbon::now()->format('d-m-Y-H').'.xlsx'; // Replace with the path to your file
        $recipient = "sotkasaitzakaz@yandex.ru"; // Replace with the recipient email address
        $subject = "Выгрузка заказов"; // Replace with your desired subject line

        Mail::to($recipient)->send(new OrdersMail($file, $subject));

        return 0;
    }
}
