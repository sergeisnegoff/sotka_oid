<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PreorderTableSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'preorder_id',
        'title',
        'active'
    ];

    public function markup(): HasOne
    {
        return $this->hasOne(PreorderSheetMarkup::class, 'preorder_sheet_id', 'id');
    }

    public function preorder(): BelongsTo
    {
        return $this->belongsTo(Preorder::class, 'preorder_id', 'id');
    }
}
