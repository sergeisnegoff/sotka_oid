<?php

namespace App\Providers;

use App\Logging\Logger;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.*', function (\Illuminate\View\View $view){
            if(!in_array($view->name(), [
                'layouts.error.404',
                'layouts.error.500',
                'layouts.app',
            ])){
                return;
            }
            $view
                ->with('categories', categoryTreeSort())
                ->with('errors', optional($view['errors'] ?? null));
        });

        Logger::register($this->app);
    }
}
