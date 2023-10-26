<?php

namespace App\Services;

class TotalsService
{
    public static function getTotalByUser() {
        return floor(collect(session('cart', []))->sum(function ($item) {
                return ($item['price'] ?? 0) * $item['quantity'];
            }) + collect(\App\Services\Preorder\PreorderService::getCart())->sum(function ($item) {
                return ($item['price'] ?? 0) * $item['quantity'];
            }));
    }
}
