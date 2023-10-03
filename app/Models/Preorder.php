<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Preorder extends Model
{
    protected $fillable = ['file_processed', 'is_finished'];

    public static function boot()
    {

        parent::boot();

        static::creating(function ($item) {
            $item->code = \Str::slug($item->title);
        });
    }

    public function sheets(): HasMany
    {
        return $this->hasMany(PreorderSheet::class, 'preorder_id', 'id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(PreorderCategory::class, 'preorder_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(PreorderProduct::class);
    }

    public function preorderCheckouts()
    {
        return $this->hasMany(PreorderCheckout::class);
    }

    public function preorderCheckoutsForCurrentManager() {
        return $this->preorderCheckouts()->whereHas('user', function ($query) {
            $query->where('manager_id', auth()->user()->managerContact->id);
        });
    }

    public function total(bool $asMerch = false) {
        $sum = 0;
        foreach ($this->preorderCheckouts()->get() as $checkout) {
            $sum += $checkout->total($asMerch);
        }
        return $sum;
    }
    // Тотал с разделением клиенты/сотка
    public function totalByType(bool $internal = false, bool $asMerch = false) {
        $sum = 0;
        foreach ($this->preorderCheckouts()->where('is_internal', $internal)->get() as $checkout) {
            $sum += $checkout->total($asMerch);
        }
        return $sum;
    }

    public function quantityByType($internal = false) {
        return PreorderCheckoutProduct::whereHas('preorderCheckout', function ($query) use ($internal) {
            $query->where('preorder_id', $this->id)->where('is_internal', $internal);
        })->sum('qty');
    }

    public function quantity()
    {
        return PreorderCheckoutProduct::whereHas('preorderCheckout', function ($query) {
            $query->where('preorder_id', $this->id);
        })->sum('qty');
    }
    public function hasSoftLimitExcess() {
        return $this->preorderCheckouts()->whereHas('products', function ($query) {
            $query->selectRaw('preorder_product_id, SUM(qty) as total_quantity')
                ->groupBy('preorder_product_id')
                ->havingRaw('total_quantity > (select soft_limit from preorder_products where id = preorder_product_id)');
        })->exists();
    }

    public static function unfinished() {
        return self::where('is_finished', false);
    }
    public static function finished() {
        return self::where('is_finished', true);
    }

    public function usersWithCheckouts() {
        return $this->preorderCheckouts->pluck('user')->unique();
    }
    public function totalByUser(User $user): float {
        $total = 0;
        $checkouts = $user->preorderCheckouts()->whereBelongsTo($this)->get();

        foreach ($checkouts as  $checkout) {
            $total += $checkout->total();
        };
        return $total;
    }
    public function totalForCurrentManager(): float {
        $total = 0;
        $checkouts = $this->preorderCheckoutsForCurrentManager()->get();
        foreach ($checkouts as $checkout) {
            $total += $checkout->total();
        }
        return $total;
    }
}
