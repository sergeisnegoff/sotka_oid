<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderSheetMarkup extends Model
{

    protected $fillable = [
        'preorder_table_sheet_id',
        'title',
        'barcode',
        'price',
        'description',
        'multiplicity',
        'multiplicity_tu',
        'container',
        'hard_limit',
        'soft_limit',
        'country',
        'packaging',
        'package_type',
        'weight',
        'season',
        'r_i',
        'image',
        'link',
        'category',
        'subcategory',
        'seasonality',
        'plant_height',
        'packaging_type',
        'culture_type',
        'frost_resistance',
        'additional_1',
        'additional_2',
        'additional_3',
        'additional_4',
    ];
}
