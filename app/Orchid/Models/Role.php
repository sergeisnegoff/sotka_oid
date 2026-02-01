<?php

namespace App\Orchid\Models;

use Orchid\Platform\Models\Role as OrchidRole;
use TCG\Voyager\Models\Permission;

class Role extends OrchidRole
{
    protected $table = 'orchid_roles';

    public function permissions()
    {

        return $this->belongsToMany(Permission::class);
    }
}
