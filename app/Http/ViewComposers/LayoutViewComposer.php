<?php

namespace App\Http\ViewComposers;

use App\Models\Product;
use Illuminate\View\View;

class LayoutViewComposer
{
    public function compose(View $view)
    {
        $cart = collect(session('cart', []));

        $products = Product::multiplicity()
            ->total()
            ->select(['products.id', 'total as total_all', 'multiplicity', 'category_id', 'products.title'])
            ->sortByCategory()
            ->orderBy('products.title')
            ->findMany($cart->keys());

        $cart_items = $products->map(fn(Product $product) => $product
            ->setRawAttributes(array_merge($product->getAttributes(), $cart->get($product->id)))
        );

        $view
            ->with('miniCartData', $cart_items)
            ->with('miniCartTotal', $cart_items->sum(fn($item) => $item['price'] * $item['quantity']))
            ->with('miniCartCount', $cart_items->count('quantity'));
    }
}
