<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactsManagersModel extends Model
{
    use HasFactory;
    protected $fillable = ['user_id'];
    protected $table = 'contacts_managers';
}
