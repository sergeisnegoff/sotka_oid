<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public static function getOrdersForCurrentManager() {
        return Order::whereHas('user', function ($query) {
            $query->where('manager_id', auth()->user()->managerContact->id);
        })->orderByDesc('id');
    }
}
