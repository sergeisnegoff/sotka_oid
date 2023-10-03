<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    public function subFilter()
    {
        return $this->hasMany('App\Models\Subfilter', 'filter_id');
    }
}
