<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
class BrandsModel extends Model
{
    use Resizable;
	protected $table = 'brands';
}
