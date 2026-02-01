<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ContactsManagersModel extends Model
{
    use AsSource, Filterable;

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

    protected array $allowedSorts = [
        'id',
        'name',
        'email',
        'phone',
        'position',
        'uuid',
        'visible',
        'user_id',
        'created_at',
        'updated_at',
    ];


    public function scopeVisible($query)
    {
        return $query->where('visible', 1);
    }

}
