<?php

namespace App\Http\Controllers;

use App\Imports\ProductUpdateImport;
use App\Mail\UpdateOrder;
use App\Models\cronSettings;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ImportController extends Controller
{
    public function products()
    {
        $time = microtime(true);
        ProductUpdateImport::make()->import(storage_path('app/1c/Price/obshii.xls'));
        return \response(microtime(true) - $time);
    }

    public function contragents()
    {
        $dom = new \DOMDocument();
        $dom->load(storage_path() . '/app/1c/Kontr/kontr.xml');

        $sheetData = [];
        foreach ($dom->getElementsByTagName('Элемент') as $child) {
            if ($child->getAttribute('Наименование')) {
                $sheetData[] = [
                    'B' => $child->getAttribute('Наименование'),
                    'C' => $child->getAttribute('Код'),
                    'U' => $child->getAttribute('Email'),
                    'M' => $child->getAttribute('КодМенеджера'),
                    'UC' => $child->getAttribute('Код')
                ];
            }
        }

        foreach ($sheetData as $row) {
            if (empty($email = trim($row['U']))) {
                continue;
            }

            $contact = DB::table('contacts_managers')->where('uuid', $row['M'])->count() ? 'contacts_managers' : 'contacts_supervisor';

            if (is_null($user = User::query()->where('email',$email)->first())) {
                $user = new User([
                    'password' => Hash::make(rand(0, 1000)),
                    'email' => $email,
                ]);
            }

            $user->fill([
                'name' => $row['B'],
                'manager_id' => $row['M'],
                'manager_table' => $contact,
                'uniq_code' => $row['C'],
            ])->save();
        }
    }

    public function orders($order = null)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path() . '/app/1c/Otgruz/' . (is_null($order) ? date('d_m_y') . '.xls' : $order));
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $order = new \stdClass();
        $orderProducts = $missingProducts = $oldProduct = [];

        foreach ($sheetData as $key => $row) {
            if (in_array($key, range(1, 2))) continue;
            if (!empty($row['B'])) {
                $order = Order::updateOrCreate(['id' => $row['L']], [
                    'status' => $row['E']
                ]);

                $products = Order::getOrderProducts($order->id);
                $productIDs = $products->pluck('id', 'product_id');
                $orderProducts = $missingProducts = collect($products)->pluck('info.oneC_7')->toArray();

            if (!empty($missingProducts))
                foreach ($missingProducts as $product)
                    if (!is_null($product)) {
                        Order::orderProducts($order->id, Product::where('oneC_7', $product)->first()->id, 0, 0, 0, 0, 0, 0);
                    }
            } else if (!empty($order)) {
                $productID = Product::where('oneC_7', 'like', $row['D'])->first();

                if(!is_null($productID)) {
                    if ($productIDs->has($productID->id)) {
                        Order::orderProducts($order->id, $productID->id, (!empty(trim($row['G'])) ? $row['G'] : 0), (!empty(trim($row['I'])) ? $row['I'] : 0), (int)$row['H'] ?: 0, !empty(trim($row['N'])) ? 1 : 0, $row['O'], $row['N']);
                        unset($missingProducts[array_search($row['D'], $missingProducts)]);
                        Log::channel('import')->info('one '.$row['O']);
                    } else {
                        Order::orderProducts($order->id, $productID->id, (!empty(trim($row['G'])) ? $row['G'] : 0), (!empty(trim($row['I'])) ? $row['I'] : 0), (int)$row['H'] ?: 0, !empty(trim($row['N'])) ? 1 : 0, $row['O'], $row['N']);
                        Log::channel('import')->info('two '.$row['O']);
                    }
                }

                $product = [];
            }
        }

        if (!empty($missingProducts))
            foreach ($missingProducts as $product)
                if (!is_null($product))
                    Order::orderProducts($order->id, Product::where('oneC_7', $product)->first()->id, 0, 0, 0, 1);


        $user_id = Order::query()->where('id', $order->id)->first();
        $userData = User::find($user_id)->first();

        Mail::to($userData->email)->send(new UpdateOrder($order, Order::getOrderProducts($order->id)));
    }

    public function managers()
    {
        $dom = new DOMDocument("1.0", "utf-8"); // Создаём XML-документ версии 1.0 с кодировкой utf-8
        $dom->load(storage_path() . "/app/1c/Kontr/managers.xml"); // Загружаем XML-документ из файла в объект DOM


        $managers = $dom->getElementsByTagName("Элемент");
        foreach ($managers as $manager) {
            $toDB = [
                'name' => $manager->getAttribute('Менеджер'),
                'uuid' => $manager->getAttribute('КодМенеджера'),
                'phone' => $manager->getAttribute('Телефон')
            ];

            DB::table('contacts_managers')->updateOrInsert(['uuid' => $toDB['uuid']], $toDB);
        }
    }

    public function cronSettings(Request $request)
    {
        cronSettings::updateOrCreate(['table' => $request->post('table')], $request->post());
    }
}
