<?php

namespace App\Http\Controllers;

use App\Category;
use App\Imports\ProductUpdateImport;
use App\Jobs\ProcessImportJob;
use App\Models\Brands;
use App\Models\cronSettings;
use App\Models\User;
use App\Order;
use App\Product;
use App\Specification;
use App\Subspecification;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Exception;

class ImportController extends Controller
{
    public function products() {
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
            if (!empty(trim($row['U']))) {
                $contact = DB::table('contacts_altay')->where('uuid', $row['M'])->count() ? 'contacts_altay' : 'contacts_regional';
                if (User::where('email', trim($row['U']))->first()) {
                    User::where('email', trim($row['U']))->update([
                        'name' => $row['B'],
                        'email' => trim($row['U']),
                        'manager_id' => $row['M'],
                        'uniq_code' => $row['C'],
                        'manager_table' => $contact
                    ]);
                } else
                    User::create([
                        'name' => $row['B'],
                        'manager_id' => $row['M'],
                        'password' => Hash::make(rand(0, 1000)),
                        'uniq_code' => $row['C'],
                        'email' => trim($row['U']),
                        'manager_table' => $contact
                    ]);
            }
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
                $order = Order::updateOrCreate(['random' => $row['L']], [
                    'status' => $row['E']
                ]);

                $products = Order::getOrderProducts($order->id);
                $orderProducts = $missingProducts = collect($products)->pluck('info.oneC_7')->toArray();

                if (!empty($missingProducts))
                    foreach ($missingProducts as $product)
                        if (!is_null($product)) {
                            Order::orderProducts($order->id, Product::where('oneC_7', (int)$product)->first()->id, 0, 0);
                        }
            } else if (!empty($order)) {
                $productID = Product::where('oneC_7', 'like', (int)$row['D'])->first();

                if (!is_null($productID)) {
                    $product = $products[array_search($productID->id, array_column($products->toArray(), 'product_id'))];

                    if (in_array((int)$row['D'], $orderProducts)) {
                        if (isset($productID->id)) {
                            Order::orderProducts($order->id, $productID->id, (empty($row['G']) ? 0 : (int)$row['G']), (!empty(trim($row['I'])) ? $row['I'] : 0), (int)$row['H'] ? : 0, !empty(trim($row['N'])) || !empty(trim($row['M'])) ? 1 : 0, $row['O'], $row['N']);
                            unset($missingProducts[array_search((int)$row['D'], $missingProducts)]);
                        }
                    } else
                        Order::orderProducts($order->id, $productID->id, (empty($row['G']) ? 0 : (int)$row['G']), (!empty(trim($row['I'])) ? $row['I'] : 0), (int)$row['H'] ? : 0, !empty(trim($row['N'])) || !empty(trim($row['M'])) ? 1 : 0, $row['O'], $row['N']);
                }

                $product = [];
            }
        }

        if (!empty($missingProducts))
            foreach ($missingProducts as $product)
                if (!is_null($product))
                    Order::orderProducts($order->id, Product::where('oneC_7', (int)$product)->first()->id, 0, 0, 0, 1);
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

            if ($manager->getAttribute('Регион') == 'Краевой менеджер')
                DB::table('contacts_altay')->updateOrInsert(['uuid' => $toDB['uuid']], $toDB);
            else
                DB::table('contacts_regional')->updateOrInsert(['uuid' => $toDB['uuid']], $toDB);
        }
    }

    public function cronSettings(Request $request)
    {
        cronSettings::updateOrCreate(['table' => $request->post('table')], $request->post());
    }
}
