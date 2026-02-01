<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactsManagersModel extends Model
{
    protected $table = 'contacts_managers';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'img',
        'uuid',
        'visible',
        'user_id',
    ];

    public function scopeVisible($query)
    {
        return $query->where('visible', 1);
    }
}
