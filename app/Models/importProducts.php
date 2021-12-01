<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class importProducts extends Model
{
    use HasFactory;
    protected $table = 'import';
    protected $fillable = ['time', 'table'];
}

