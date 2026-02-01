<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Models\Role as VoyagerRole;

class VoyagerServiceProvider extends ServiceProvider
{
    public function register()
    {
// Явно указываем использовать модель Voyager для ролей
        $this->app->singleton('voyager.role', function () {
            return new VoyagerRole;
        });
    }
}
