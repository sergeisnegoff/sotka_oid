<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProfileAddress extends Model
{
    use HasFactory;
    protected $table = 'user_address';

    public static function store($data) {
        return DB::table((new self)->getTable())->insert($data);
    }

    public static function getByID($id) {
        if ($id == 99)
            return (object)['title' => 'Самовывоз'];
        return DB::table((new self)->getTable())->where('id', $id)->first();
    }
}
