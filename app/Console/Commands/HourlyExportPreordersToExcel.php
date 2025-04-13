<?php

namespace App\Console\Commands;

use App\Mail\NewOrdersMail;
use App\Models\Order;
use App\Models\Preorder;
use App\Models\PreorderCheckout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HourlyExportPreordersToExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:preorder_hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $currentHour = now()->hour;
        $betweenTime = [];

        if ($currentHour === 8) {
            $betweenTime = [
                now()->subDay()->setTime(20, 0),
                now()->setTime(8, 0),
            ];
        } elseif ($currentHour >= 9 && $currentHour <= 17) {
            $betweenTime = [
                now()->setTime($currentHour - 1, 0),
                now()->setTime($currentHour, 0),
            ];
        } elseif ($currentHour === 20) {
            $betweenTime = [
                now()->setTime(17, 0),
                now()->setTime(20, 0),
            ];
        } else {
            return 0;
        }
        //$betweenTime[0] =now()->subDays(5)->setTime(8, 0);
        $orders = PreorderCheckout::with('products.preorder_product', 'user', 'preorder')
            ->whereBetween('created_at', $betweenTime)
            ->latest()
            ->get();
        //dd($orders);

        if (count($orders) > 0) {
            $spreadsheet = new Spreadsheet();

            $worksheet = $spreadsheet->getActiveSheet();

            $worksheet->setCellValue('A1', 'Номер заказа');
            $worksheet->setCellValue('B1', 'ФИО заказчика');
            $worksheet->setCellValue('C1', 'Предзаказ');
            $worksheet->setCellValue('D1', 'Дата заказа');


            $currentRow = 3;
            $files = [];
            $recipient = "sotkasaitzakaz@yandex.ru"; // Replace with the recipient email address
            //$recipient = "magzip23@gmail.com";
            $subject = "Выгрузка предзаказов";
            foreach ($orders as $order) {

                $worksheet->setCellValue('A'.$currentRow, $order->id);
                $worksheet->setCellValue('B'.$currentRow, $order->user->name);
                $worksheet->setCellValue('C'.$currentRow, $order->preorder->title);
                $worksheet->setCellValue('D'.$currentRow, $order->created_at->format('d.m.Y H:i:s'));


                $worksheet->setCellValue('A'.$currentRow + 2, 'Название товара');
                $worksheet->setCellValue('C'.$currentRow + 2, 'Цена');
                $worksheet->setCellValue('D'.$currentRow + 2, 'Кол-во');
                $worksheet->setCellValue('E'.$currentRow + 2, 'Общая стоимость');

                $productRow = $currentRow + 3;

                foreach ($order->products as $product) {
                    $worksheet->setCellValue('A'.$productRow, $product->preorder_product->title);
                    $worksheet->setCellValue('C'.$productRow, $product->preorder_product->price);
                    $worksheet->setCellValue('D'.$productRow, $product->qty);
                    $worksheet->setCellValue('E'.$productRow, number_format($product->qty * $product->preorder_product->price, 0, '.', ' '));

                    $productRow++;
                }

                if (Storage::exists('public/excel/preorders/') === false) {
                    mkdir(storage_path('app/public') . '/excel/preorders/', 0777, true);
                }

                $writer = new Xlsx($spreadsheet);
                $fileName = storage_path('app/public').'/excel/preorders/preorder-'.$order->id.'_'.\Carbon\Carbon::now()->format('d-m-Y-H').'.xlsx';
                $writer->save($fileName);
                $files[] = $fileName;
            }
            Mail::to($recipient)->send(new NewOrdersMail($files, $subject));
            $recipient = "magzip23@gmail.com";
            Mail::to($recipient)->send(new NewOrdersMail($files, $subject));
        }
        return 0;
    }
}
