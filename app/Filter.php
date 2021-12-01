<?php


namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Subfilter;
class Filter extends Model
{
    public function subFilter()
    {
        return $this->hasMany('App\Subfilter','filter_id');
    }
}
