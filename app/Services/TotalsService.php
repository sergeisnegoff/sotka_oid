<?php

namespace App\Services;

use App\Models\Preorder;

class TotalsService
{
    public static function getUserTotalByOrders() {
        return floor(collect(session('cart', []))->sum(function ($item) {
                return ($item['price'] ?? 0) * $item['quantity'];
            }));
    }

    public static function getUserTotalByPreorders() {
        return floor(collect(\App\Services\Preorder\PreorderService::getCart())->sum(function ($item) {
            return ($item['price'] ?? 0) * $item['quantity'];
        }));
    }

    public static function getUserTotalByPreorder(Preorder $preorder) {
        return floor(collect(\App\Services\Preorder\PreorderService::getCart())->sum(function ($item) use ($preorder) {
            return $item['preorder_id'] == $preorder->id ? (($item['price'] ?? 0) * $item['quantity']) : 0;
        }));
    }
}
