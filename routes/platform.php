<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\Analytics\ProductAnalyticsScreen;
use App\Orchid\Screens\Manager\ManagerEditScreen;
use App\Orchid\Screens\Manager\ManagerListScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Brand\BrandEditScreen;
use App\Orchid\Screens\Brand\BrandListScreen;
use App\Orchid\Screens\Category\CategoryEditScreen;
use App\Orchid\Screens\Category\CategoryListScreen;
use App\Orchid\Screens\Preorder\PreorderEditScreen;
use App\Orchid\Screens\Preorder\PreorderListScreen;
use App\Orchid\Screens\Product\ProductEditScreen;
use App\Orchid\Screens\Product\ProductListScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

Route::screen('/analytics/products', ProductAnalyticsScreen::class)
    ->name('platform.analytics.products')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Аналитика товаров', route('platform.analytics.products')));

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

// Route::screen('idea', Idea::class, 'platform.screens.idea');

// Platform > Products
Route::screen('products', ProductListScreen::class)
    ->name('platform.systems.products')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Товары', route('platform.systems.products')));

Route::screen('products/create', ProductEditScreen::class)
    ->name('platform.systems.products.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.products')
        ->push('Создание', route('platform.systems.products.create')));

Route::screen('products/{product}/edit', ProductEditScreen::class)
    ->name('platform.systems.products.edit')
    ->breadcrumbs(fn (Trail $trail, $product) => $trail
        ->parent('platform.systems.products')
        ->push($product->title ?? 'Редактирование', route('platform.systems.products.edit', $product)));

// Platform > Categories
Route::screen('categories', CategoryListScreen::class)
    ->name('platform.systems.categories')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Категории', route('platform.systems.categories')));

Route::screen('categories/create', CategoryEditScreen::class)
    ->name('platform.systems.categories.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.categories')
        ->push('Создание', route('platform.systems.categories.create')));

Route::screen('categories/{category}/edit', CategoryEditScreen::class)
    ->name('platform.systems.categories.edit')
    ->breadcrumbs(fn (Trail $trail, $category) => $trail
        ->parent('platform.systems.categories')
        ->push($category->title ?? 'Редактирование', route('platform.systems.categories.edit', $category)));

// Platform > Brands
Route::screen('brands', BrandListScreen::class)
    ->name('platform.systems.brands')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Бренды', route('platform.systems.brands')));

Route::screen('brands/create', BrandEditScreen::class)
    ->name('platform.systems.brands.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.brands')
        ->push('Создание', route('platform.systems.brands.create')));

Route::screen('brands/{brand}/edit', BrandEditScreen::class)
    ->name('platform.systems.brands.edit')
    ->breadcrumbs(fn (Trail $trail, $brand) => $trail
        ->parent('platform.systems.brands')
        ->push($brand->title ?? 'Редактирование', route('platform.systems.brands.edit', $brand)));

Route::screen('managers', ManagerListScreen::class)
    ->name('platform.systems.managers')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Менеджеры', route('platform.systems.managers')));

Route::screen('managers/create', ManagerEditScreen::class)
    ->name('platform.systems.managers.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.managers')
        ->push(__('Create'), route('platform.systems.managers.create')));

Route::screen('managers/{manager}/edit', ManagerEditScreen::class)
    ->name('platform.systems.managers.edit')
    ->breadcrumbs(fn (Trail $trail, $manager) => $trail
        ->parent('platform.systems.managers')
        ->push($manager->name ?? 'Редактирование', route('platform.systems.managers.edit', $manager)));

// Platform > Preorders
Route::screen('preorders', PreorderListScreen::class)
    ->name('platform.systems.preorders')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Предзаказы', route('platform.systems.preorders')));

Route::screen('preorders/create', PreorderEditScreen::class)
    ->name('platform.systems.preorders.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.preorders')
        ->push('Создание', route('platform.systems.preorders.create')));

Route::screen('preorders/{preorder}/edit', PreorderEditScreen::class)
    ->name('platform.systems.preorders.edit')
    ->breadcrumbs(fn (Trail $trail, $preorder) => $trail
        ->parent('platform.systems.preorders')
        ->push($preorder->title ?? 'Редактирование', route('platform.systems.preorders.edit', $preorder)));
