<?php

namespace App\Providers;

use App\FormFields\TimeStampFormField;
use App\Logging\Logger;
use App\Models\Category;
use App\Models\Preorder;
use App\Services\Preorder\PreorderService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Voyager::addFormField(TimeStampFormField::class);
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
            $view = $this->addPreorderMinimalCost($view);
        });

        Logger::register($this->app);
    }
    public function addPreorderMinimalCost($view) {
        if (count(PreorderService::getCart())) {
            $cart = PreorderService::getCart();
            $preorder = Preorder::find(Arr::first($cart)['preorder_id']);
            if ($preorder) {
                return $view
                    ->with('preorder_minimal', $preorder->min_order);
            }
        }
        return $view;
    }
}
