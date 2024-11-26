<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderCheckoutProduct extends Model
{
    use HasFactory;

    protected $fillable = ['preorder_checkout_id', 'preorder_product_id', 'qty'];

    public function preorder_product() {
        return $this->belongsTo(PreorderProduct::class);
    }

    public function total(bool $asMerch = false) {
        return $asMerch ? (float)$this->preorder_product->merch_price * (float)$this->qty : (float)$this->preorder_product->price * (float)$this->qty;
    }

    public function preorderCheckout() {
        return $this->belongsTo(PreorderCheckout::class);
    }
}
