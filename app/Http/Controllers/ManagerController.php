<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Preorder\PreorderCartController;
use App\Http\Controllers\Preorder\PreorderController;
use App\Models\Order;
use App\Models\Preorder;
use App\Models\PreorderCheckout;
use App\Models\User;
use App\Services\OrderService;
use App\Services\Preorder\PreorderService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ManagerController extends Controller
{
    public function clients()
    {
        $managerClients = UserService::usersPinnedOnManager(auth()->user());
        $filterSorting = \request()->sorting;
        $filterName = \request()->name;
        if ($filterSorting)
            switch ($filterSorting) {
                case "ASC":
                    $managerClients = $managerClients->orderBy('name');
                    break;
                case "DESC":
                    $managerClients = $managerClients->orderByDesc('name');
                    break;
            }
        if ($filterName)
            $managerClients = $managerClients->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($filterName).'%']);
        $managerClients = $managerClients->paginate(15);
        $paginator = $managerClients;
        $page = 'index';
        $subPage = 'index';
        return view('manager.clients.index', compact('managerClients', 'paginator', 'page', 'subPage', 'filterSorting', 'filterName'));
    }

    public function clientOrders()
    {
        $managerClients = UserService::usersPinnedOnManager(auth()->user())->with('orders');
        $filterSorting = \request()->sorting;
        $filterName = \request()->name;
        if ($filterSorting)
            switch ($filterSorting) {
                case "ASC":
                    $managerClients = $managerClients->orderBy('name');
                    break;
                case "DESC":
                    $managerClients = $managerClients->orderByDesc('name');
                    break;
            }
        if ($filterName)
            $managerClients = $managerClients->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($filterName).'%']);
        $managerClients = $managerClients->paginate(15);
        $paginator = $managerClients;
        $page = 'index';
        $subPage = 'orders';
        return view('manager.clients.list-orders-preorders', compact('managerClients', 'page', 'subPage', 'paginator', 'filterSorting', 'filterName'));
    }

    public function clientPreorders()
    {
        $managerClients = UserService::usersPinnedOnManager(auth()->user())->whereHas('preorderCheckouts')->with('preorderCheckouts');
        $filterSorting = \request()->sorting;
        $filterName = \request()->name;
        if ($filterSorting)
            switch ($filterSorting) {
                case "ASC":
                    $managerClients = $managerClients->orderBy('name');
                    break;
                case "DESC":
                    $managerClients = $managerClients->orderByDesc('name');
                    break;
            }
        if ($filterName)
            $managerClients = $managerClients->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($filterName).'%']);
        $managerClients = $managerClients->paginate(15);
        $paginator = $managerClients;
        $page = 'index';
        $subPage = 'preorders';
        return view('manager.clients.list-orders-preorders', compact('managerClients', 'page', 'subPage', 'paginator', 'filterSorting', 'filterName'));
    }

    public function clientPreorderCart(User $user)
    {
        $cart = PreorderService::getFullUserCart($user->id);
        $cartKeys = collect(array_keys($cart));
        $page = 'preorder_cart';
        $preorder_minimal = 0;
        if (count($cart)) {
            $preorder_minimal = (Preorder::find(Arr::first($cart)['preorder_id']))->min_order ?? 0;
        }

        /*foreach ($cart as &$item) {
            $item['multiplicity_tu'] = PreorderProduct::find($item['id'])->multiplicity_tu;
        }*/

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

        return view('manager.clients.preorder-cart', compact('cart', 'cartKeys', 'page', 'user', 'preorder_minimal'));
    }

    public function orders()
    {
        $orders = OrderService::getOrdersForCurrentManager();
        switch (true) {
            case \request()->from_date:
                $orders = $orders->whereDate('created_at', '>=', \request()->from_date);
            case \request()->to_date:
                $orders = $orders->whereDate('created_at', '<=', \request()->to_date);
        }
        $orders = $orders->paginate(15);
        $paginator = $orders;
        $page = 'orders';
        return view('manager.orders', compact('orders', 'page', 'paginator'));
    }

    public function preorders()
    {
        $preorders = Preorder::where('is_finished', false)->orWhere(function ($query) {
            $query->where('is_finished', true)
                ->where('end_date', '>=', now()->subMonths(6));
        })->with('preorderCheckoutsForCurrentManager', 'preorderCheckoutsForCurrentManager.user', 'preorderCheckoutsForCurrentManager.products')->paginate(15);
        /*$checkouts = PreorderCheckout::whereHas('user', function ($query) {
            $query->where('manager_id', auth()->user()->managerContact->id);
        });
        $preorder_ids = $checkouts->pluck('preorder_id')->unique();
        $preorders = Preorder::whereIn('id', $preorder_ids)->paginate(15);
        $data = [];
        foreach ($preorders as $preorder) {
            $data[] = [
                'preorder' => $preorder,
                'users' => User::where('manager_id', auth()->user()->managerContact->id)->whereHas('preorderCheckouts', function ($query) use ($preorder) {
                    $query->where('preorder_id', $preorder->id);
                })->with(['preorderCheckouts', 'preorderCheckouts.products', 'preorderCheckouts.products.preorder_product'])->get(),
            ];
        }*/
        $users =
        $paginator = $preorders;
        $page = 'preorders';
        return view('manager.preorders', compact('preorders', 'page', 'paginator'));
    }

    public function uploadClientXlsx(User $user)
    {
        $preorders = PreorderService::getActivePreorders()->whereNotNull('client_file')->whereNotNull('client_qty_field')->get();
        $page = 'upload';
        $isManager = true;
        return view('manager.clients.upload', compact('preorders', 'user', 'page', 'isManager'));
    }

    public function concreteClientOrder(User $user, int $order)
    {
        // Наименьший костыль для того, чтобы использовать шаблон отображения заказов и тут
        $orders = Order::where('id', $order)->paginate(1);
        return view('manager.clients.orders', [
            'orders' => $orders,
            'page' => 'order',
            'user' => $user
        ]);
    }

    public function concreteClientOrders(User $user)
    {
        $orders = Order::getUserCurrentOrders($user->id);
        return view('manager.clients.orders', [
            'orders' => $orders,
            'page' => 'orders',
            'user' => $user
        ]);
    }

    public function concreteClientOrdersHistory(User $user)
    {
        $orders = Order::getUserCurrentOrders($user->id, true);
        return view('manager.clients.orders', [
            'orders' => $orders,
            'page' => 'orders_history',
            'user' => $user
        ]);
    }
    public function concreteClientPreordersHistory(User $user)
    {
        $orders = PreorderCheckout::getUserCurrentPreorders($user->id);
        return view('manager.clients.preorders_history', [
            'cart' => $orders,
            'page' => 'preorders_history',
            'user' => $user
        ]);
    }

    public function preorderAsUser(User $user, PreorderCartController $controller) {
        $preorder_id = array_key_first(($controller->cart())["cart"]);
        request()->merge(["preorder_id" => $preorder_id]);
        $controller->create(request(), $user);
        PreorderService::removePreorderFromCart($preorder_id, auth()->user()->id);
        return response()->json(PreorderCheckout::forUser($user)->orderByDesc('created_at')->with('preorder')->first());
    }

}
