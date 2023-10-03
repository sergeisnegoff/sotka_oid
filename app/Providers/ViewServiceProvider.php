<?php

namespace App\Providers;

use App\Http\ViewComposers\LayoutViewComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer([
            'layouts.app',
            'profile.components.mini-basket'
        ], LayoutViewComposer::class);
    }
}
