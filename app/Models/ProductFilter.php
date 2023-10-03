<?php

namespace App\Models;

use Illuminate\Http\Request;

class ProductFilter
{
    protected $builder;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function apply($builder) {
        $this->builder = $builder;
        foreach ($this->filters() as $filter => $value){
            if(method_exists($this, $filter)){
                $this->$filter($value);
            }
        }
        return $this->builder;
    }
    public function subFilter($value){
        $this->builder->whereHas('subFilter', function ($query) use ($value){
            $query->whereIn('title',  $value);
        });
    }

    public function filters(){
        return $this->request->all();
    }

}
