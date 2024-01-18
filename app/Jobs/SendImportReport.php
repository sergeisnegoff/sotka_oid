<?php

namespace App\Jobs;

use App\Mail\WrongImportProducts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendImportReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $importReport = cache('import_report');
        if (!empty($importReport)) {
            $wrongProducts = array_key_exists('wrong_rows', $importReport) ? $importReport['wrong_rows'] : [];
            $newProducts = array_key_exists('new_rows', $importReport) ? $importReport['new_rows'] : [];
            if (!empty($wrongProducts) || !empty($newProducts)) {
                Log::channel('import')->info('started email send', compact('importReport'));

                $recipient = "sotkasaitzakaz@yandex.ru"; // Replace with the recipient email address
                //$recipient = "magzip23@gmail.com";
                $subject = "Загрузка заказов"; // Replace with your desired subject line

                Mail::to($recipient)->send(new WrongImportProducts($wrongProducts, $newProducts, $subject));
                Log::channel('import')->info('end email send', compact('importReport'));
            }
            else {
                Log::channel('import')->info('no changes found', compact('importReport'));
            }

            cache(['import_report' => []]);
        }


        $updatePreorder = cache('update_preorder');

        if (!empty($updatePreorder)) {
            $noBarcodeRows = array_key_exists('no_barcode_rows', $updatePreorder) ? $updatePreorder['no_barcode_rows'] : [];
            $noProductsRows = array_key_exists('no_products_rows', $updatePreorder) ? $updatePreorder['no_products_rows'] : [];
            if (!empty($noBarcodeRows) || !empty($noProductsRows)) {
                Log::channel('import')->info('started email send for updated Preorder', compact('updatePreorder'));

                //$recipient = "sotkasaitzakaz@yandex.ru"; // Replace with the recipient email address
                $recipient = "magzip23@gmail.com";
                $subject = "Обновление предзаказа"; // Replace with your desired subject line

                Mail::to($recipient)->send(new WrongImportProducts($noBarcodeRows, $noProductsRows, $subject));
                Log::channel('import')->info('end email send for updated Preorder', compact('updatePreorder'));
            }
            else {
                Log::channel('import')->info('no changes found for updated Preorder', compact('updatePreorder'));
            }

            cache(['update_preorder' => []]);
        }
    }
}
