<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdvProductsCategory extends Model
{
    use HasFactory;

    public static function getContent() {
        $model = new self;
        $items = DB::table($model->getTable())->get()->each(function ($item) {
            $item->items = advProducts::where('category_id', $item->id)->orderBy('sorder', 'DESC')->get();
        });

        foreach ($items as $key => $item) {
            if (!count($item->items))
                unset($items[$key]);
        }

        return array_values($items->toArray());
    }
}

