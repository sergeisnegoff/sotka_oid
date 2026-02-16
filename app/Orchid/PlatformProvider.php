<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Основные показатели')
                ->icon('bs.speedometer2')
                ->title('Аналитика')
                ->route(config('platform.index')),

            Menu::make('Аналитика товаров')
                ->icon('bs.bar-chart-line')
                ->route('platform.analytics.products'),

//            Menu::make('Sample Screen')
//                ->icon('bs.collection')
//                ->route('platform.example')
//                ->badge(fn () => 6),
//
//            Menu::make('Form Elements')
//                ->icon('bs.card-list')
//                ->route('platform.example.fields')
//                ->active('*/examples/form/*'),
//
//            Menu::make('Layouts Overview')
//                ->icon('bs.window-sidebar')
//                ->route('platform.example.layouts'),
//
//            Menu::make('Grid System')
//                ->icon('bs.columns-gap')
//                ->route('platform.example.grid'),
//
//            Menu::make('Charts')
//                ->icon('bs.bar-chart')
//                ->route('platform.example.charts'),
//
//            Menu::make('Cards')
//                ->icon('bs.card-text')
//                ->route('platform.example.cards')
//                ->divider(),

            Menu::make('Товары')
                ->icon('bs.box-seam')
                ->route('platform.systems.products')
                ->permission('platform.systems.products')
                ->title('Каталог'),

            Menu::make('Категории')
                ->icon('bs.folder')
                ->route('platform.systems.categories')
                ->permission('platform.systems.products'),

            Menu::make('Бренды')
                ->icon('bs.bookmark')
                ->route('platform.systems.brands')
                ->permission('platform.systems.products'),

            Menu::make('Предзаказы')
                ->icon('bs.cart-check')
                ->route('platform.systems.preorders')
                ->permission('platform.systems.products'),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Управление пользователями')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),

            Menu::make('Менеджеры')
                ->icon('bs.shield')
                ->route('platform.systems.managers')
                ->permission('platform.systems.managers')
                ->divider()
                ->title(__('Отдел продаж')),

        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users'))
                ->addPermission('platform.systems.managers', 'Менеджеры')
                ->addPermission('platform.systems.products', 'Товары')
                ->addPermission('platform.systems', 'Полный доступ')
                ->addPermission('platform.index', 'Доступ к админке'),

        ];
    }
}
