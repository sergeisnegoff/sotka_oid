<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreorderCategory extends Model
{
    protected $fillable = [
        'title',
        'preorder_category_id',
        'preorder_id',
        'preorder_table_sheet_id',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(PreorderProduct::class, 'preorder_category_id', 'id');
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(PreorderCategory::class, 'preorder_category_id', 'id');
    }

    public function preorder(): BelongsTo
    {
        return $this->belongsTo(Preorder::class, 'preorder_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PreorderCategory::class, 'preorder_category_id', 'id');
    }

    public function scopeRoot($query): void {
        $query->whereNull('preorder_category_id');
    }

    public function isRoot(): bool {
        return !$this->preorder_category_id;
    }

    public function childs(): HasMany {
        return $this->hasMany(self::class, 'preorder_category_id',  'id');
    }
}
