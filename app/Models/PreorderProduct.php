<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreorderProduct extends Model
{
    protected $fillable = [
        'sku',
        'title',
        'image',
        'description',
        'link',
        'price',
        'preorder_category_id',
        'multiplicity',
        'multiplicity_tu',
        'container',
        'country',
        'packaging',
        'package_type',
        'weight',
        'season',
        'r_i',
        'barcode',
        'cell_number',
        'preorder_id',
        'seasonality',
        'plant_height',
        'packaging_type',
        'package_amount',
        'culture_type',
        'frost_resistance',
        'additional_1',
        'additional_2',
        'additional_3',
        'additional_4',
        'soft_limit',
        'hard_limit',
        'merch_price'
    ];

    public function getMultiplicityTuAttribute($value)
    {
        return empty($value) ? 1 : $value;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PreorderCategory::class, 'preorder_category_id', 'id');
    }

    public function preorder(): BelongsTo
    {
        return $this->belongsTo(Preorder::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function checkouts() {
        return $this->hasMany(PreorderCheckoutProduct::class);
    }

    public function getTotalQty() {
        return $this->checkouts()->sum('qty');
    }

    public function getTotalQtyByType($internal = false)
    {
        return $this->checkouts()->whereHas('preorderCheckout', function ($query) use ($internal) {
            $query->where('is_internal', $internal);
        })->sum('qty');

    }
}
