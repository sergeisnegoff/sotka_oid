<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Subfilter extends Model
{
    public function filter()
    {
        return $this->belongsTo('App\Filter','filter_id');
    }

}
