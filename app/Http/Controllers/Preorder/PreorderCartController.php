<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Mail\NewOrdersMail;
use App\Mail\SuccessPreorder;
use App\Models\Page;
use App\Models\Preorder;
use App\Models\PreorderCheckout;
use App\Models\PreorderCheckoutProduct;
use App\Models\PreorderProduct;
use App\Models\User;
use App\Services\Preorder\PreorderService;
use Doctrine\Common\Cache\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PreorderCartController extends Controller
{
    public function index() {
        $cart = PreorderService::getFullCart();
        $cartKeys = collect(array_keys($cart));
        $page = 'preorder';

        $address = auth()->user()->address()->get() ?? collect();
        $user = auth()->user();
        $preorder_minimal = 0;
        if (count($cart)) {
            $preorder_minimal = (Preorder::find(Arr::first($cart)['preorder_id']))->min_order ?? 0;
        }

        /*foreach ($cart as &$item) {
            $item['multiplicity_tu'] = PreorderProduct::find($item['id'])->multiplicity_tu;
        }*/

        $cart = collect($cart)->groupBy('preorder_id')->toArray();

        foreach ($cart as $preorder_id => &$item) {
            $preorder = Preorder::find($preorder_id);
            if (!$preorder) {
                continue;
            }
            $preorder = $preorder->toArray();
            $preorder['products'] = $item;
            $preorder['total_amount'] = 0;
            foreach ($item as $product) {
                $preorder['total_amount'] += $product['price'] * $product['quantity'];
            }

            $preorder['prepay_amount'] = round($preorder['total_amount'] / 100 * $preorder['prepay_percent'], 2);

            $item = $preorder;
        }

        return view('preorder.cart', compact('cart', 'cartKeys', 'page', 'address', 'user', 'preorder_minimal'));
    }

    public function cart(User $user = null)
    {
        if (!$user) $user = auth()->user();
        $cart = PreorderService::getFullUserCart($user->id);

        foreach ($cart as &$item) {
            $product = PreorderProduct::find($item['id']);
            if (!$product) {
                PreorderService::removeFromCart($item['id']);
                unset($cart[$item['id']]);
                continue;
            }
            $item['multiplicity_tu'] = PreorderProduct::find($item['id'])->multiplicity_tu;
        }

        $cart = collect($cart)->groupBy('preorder_id')->toArray();

        foreach ($cart as $preorder_id => &$item) {
            $preorder = Preorder::find($preorder_id)->toArray();
            $preorder['products'] = $item;
            $preorder['total_amount'] = 0;
            foreach ($item as $product) {
                $preorder['total_amount'] += $product['price'] * $product['quantity'];
            }

            $preorder['prepay_amount'] = round($preorder['total_amount'] / 100 * $preorder['prepay_percent'], 2);

            $item = $preorder;
        }

        return [
            'status' => 'success',
            'cart' => $cart
        ];
    }

    public function empty(Preorder $preorder, User $user = null)
    {
        if ($user === null) $user = auth()->user();
        PreorderService::removePreorderFromCart($preorder->id, $user->id);

        return back();
    }

    public function create(Request $request, User $user = null, $asUser=false)
    {
        $authUser = auth()->user();
        if (!$user) $user = $authUser;
        $history = cache()->get('preorder_history_' . $user->id) ?? [];
        $cart = $this->cart($asUser ? $authUser : $user)['cart'];
        if (!isset($cart[$request->get('preorder_id')]))
            return redirect()->back();
        $order = $cart[$request->get('preorder_id')];

        $props = [
            'user_id' => $user->id,
            'preorder_id' => $order['id']
        ];
        if (auth()->user()->role_id === 1 && auth()->user()->id == $user->id) {
            $props['is_internal'] = true;
        }

        $checkoutedPreorder = PreorderCheckout::create($props);
        //dd($checkoutedPreorder);
        foreach ($order['products'] as $product) {
            PreorderCheckoutProduct::create([
                'preorder_checkout_id' => $checkoutedPreorder->id,
                'preorder_product_id' => $product['id'],
                'qty' => $product['quantity']
            ]);

            $reorderProduct = PreorderProduct::find($product['id']);
            if (!is_null($reorderProduct->hard_limit)) {
                $hardLimit = $reorderProduct->hard_limit - $product['quantity'];
                if ($hardLimit < 0) $hardLimit = 0;
                $reorderProduct->hard_limit = $hardLimit;
                $reorderProduct->save();
            }
        }

        try {
            $preorders = collect([]);
            $newOrder = PreorderCheckout::with('products.preorder_product', 'user', 'preorder')->find($checkoutedPreorder->id);
            if ($newOrder && $newOrder->preorder->is_internal && $newOrder->preorder->is_one_c) {
                $preorders->push($newOrder);
                $orderJson = json_encode($preorders);
                $datetime = date('d_m_Y-H_i_s');
                $filename = "preorders/export/{$datetime}_preorder_id-{$newOrder->preorder->id}.json";
                $disk = Storage::disk('public');
                $disk->put($filename, $orderJson);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }


        try {
            //$to = 'sotkapredzakaz@mail.ru';
            //$to = 'magzip23@gmail.com';
            $files = [];
            $subject = "Выгрузка предзаказов";
            $to = 'sotkapredzakaz2@yandex.ru';
            if (setting('admin.export_preorders_to_manager')) {
                $files = [];
                $spreadsheet = new Spreadsheet();

                $worksheet = $spreadsheet->getActiveSheet();

                $worksheet->setCellValue('A1', 'Номер заказа');
                $worksheet->setCellValue('B1', 'ФИО заказчика');
                $worksheet->setCellValue('C1', 'Предзаказ');
                $worksheet->setCellValue('D1', 'Дата заказа');


                $currentRow = 3;
                $worksheet->setCellValue('A'.$currentRow, $newOrder->id);
                $worksheet->setCellValue('B'.$currentRow, $newOrder->user->name);
                $worksheet->setCellValue('C'.$currentRow, $newOrder->preorder->title);
                $worksheet->setCellValue('D'.$currentRow, $newOrder->created_at->format('d.m.Y H:i:s'));

                $worksheet->setCellValue('A'.$currentRow+1, $newOrder->total());

                $worksheet->setCellValue('A'.$currentRow + 2, 'Название товара');
                $worksheet->setCellValue('C'.$currentRow + 2, 'Цена');
                $worksheet->setCellValue('D'.$currentRow + 2, 'Кол-во');
                $worksheet->setCellValue('E'.$currentRow + 2, 'Общая стоимость');

                $productRow = $currentRow + 3;

                foreach ($newOrder->products as $product) {
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
                $fileName = storage_path('app/public').'/excel/preorders/preorder-'.$newOrder->id.'_'.\Carbon\Carbon::now()->format('d-m-Y-H').'.xlsx';
                $writer->save($fileName);
                $files[] = $fileName;
            }
            if ($checkoutedPreorder->user->manager_id) {
                $managerEmail = $checkoutedPreorder->user->managerContact->email;
                if (count($files) && setting('admin.export_preorders_to_manager')) {
                    \Mail::to($managerEmail)->send(
                        new SuccessPreorder($checkoutedPreorder, true)
                    );
                    Mail::to($managerEmail)->send(new NewOrdersMail($files, $subject));
                    Mail::to("magzip23@gmail.com")->send(new NewOrdersMail($files, $subject));
                }

            }
            if (setting('admin.export_orders_to_email')) {
                \Mail::to($to)->send(
                    new SuccessPreorder($checkoutedPreorder, true)
                );
                //Mail::to($to)->send(new NewOrdersMail($files, $subject));
            }

            \Mail::to($checkoutedPreorder->user)->send(
                new SuccessPreorder($checkoutedPreorder)
            );

        } catch (\Exception $e) {
            \Log::channel('orders')->error($e->getMessage(), [
                'exception' => $e,
                'data' => $checkoutedPreorder
            ]);
        }

        //cache()->put('preorder_history_' . auth()->id(), $history);

        $userId = $asUser ? $authUser->id : $user->id;

        PreorderService::removePreorderFromCart($request->get('preorder_id'), $userId);

        return redirect()->route('preorders_history');
    }
}
