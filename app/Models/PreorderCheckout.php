<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderCheckout extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'preorder_id', 'is_internal'];

    public static function forUser($user = null)
    {
        if (!$user) $user = auth()->user();
        return self::where('user_id', $user->id);
    }

    public function prepay_amount()
    {
        return ($this->preorder->prepay_percent / 100) * $this->total();
    }

    public function total(bool $asMerch = false) {
        $final = 0;
        foreach ($this->products as $product) {
            $final += $product->total($asMerch);
        }
        return $final;
    }

    public function products()
    {
        return $this->hasMany(PreorderCheckoutProduct::class);
    }

    public function preorder()
    {
        return $this->belongsTo(Preorder::class);
    }

    public static function userCheckoutsForPreorder(Preorder $preorder) {
        return self::where('preorder_id', $preorder->id)
            ->where('user_id', auth()->id())->get();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    public static function getUserCurrentPreorders($id)
    {
        return static::query()
            ->where('user_id', $id)
            ->orderBy('created_at', 'DESC')
            ->with(['products'])
            ->paginate(15);
    }
}
