<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;


Route::get('/',['as'=>'jquery.load_more',HomeController::class, 'index'])->name('home');

Route::post('/reset-password', [\App\Http\Controllers\Controller::class, 'resetPassword'])->name('reset-password');

Route::get('/img/{path}/{img}', [\App\Http\Controllers\ImageController::class, 'show'])->where('path', '.*');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::post('/salesystems', [\App\Http\Controllers\Voyager\SalesystemController::class, 'updateTable'])->name('voyager.salesystems.updateTable');
    Route::post('/brandsale', [\App\Http\Controllers\Voyager\BrandSaleController::class, 'updateTable'])->name('voyager.brandsales.updateTable');

    Route::post('/users/{id}/updateCategories', [\App\Http\Controllers\ProfileController::class, 'updateTableCategories'])->name('voyager.users-categories.updateTable');
    Route::post('/users/{id}/updateBrands', [\App\Http\Controllers\ProfileController::class, 'updateTableBrands'])->name('voyager.users-brands.updateTable');

});
Route::post('/users/active/{id}', [\App\Http\Controllers\ProfileController::class, 'activeAccount'])->name('voyager.users-active');

Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{title}', [ProductController::class, 'getProductsCat'])->name('products_parent_cats');
Route::get('/products/{id}/{title}', [ProductController::class, 'getProductsSubCat'])->name('products_cats');

Route::get('/product/{id}', [ProductController::class, 'getProduct'])->name('product');
Route::get('/searchProducts', [ProductController::class, 'searchProducts'])->name('searchProducts');
Route::post('add-to-cart/{id}', [HomeController::class, 'addToCart'])->name('addToCart');
Route::get('add-to-cart/{id}', [HomeController::class, 'addToCart']);
Route::patch('update-cart/{id}', [HomeController::class, 'update']);
Route::delete('remove-from-cart',[HomeController::class, 'remove']);
Route::get('basket/load', [CartController::class, 'loadMini']);

Auth::routes();
Route::name('cart.')->prefix('cart')->group(function () {
    Route::post('/delete/{id?}', [CartController::class, 'delete'])->name('delete');
    Route::get('/empty', [CartController::class, 'empty'])->name('empty');
    Route::put('/create', [CartController::class, 'create'])->name('create');
    Route::post('/update-count', [CartController::class, 'updateCount'])->name('updateQty');
    Route::post('reorder/{id?}', [\App\Http\Controllers\ProfileController::class, 'reOrders'])->name('reorder');
});

Route::prefix('import')->name('import')->group(function () {
    Route::get('products', [\App\Http\Controllers\ImportController::class, 'products']);
});

Route::prefix('cron')->name('cron.')->group(function () {
    Route::post('save', [\App\Http\Controllers\ImportController::class, 'cronSettings'])->name('save');
});

Route::get('update-order/{filename?}', [\App\Http\Controllers\ImportController::class, 'orders']);
Route::get('update-kontr/{filename?}', [\App\Http\Controllers\ImportController::class, 'contragents']);
Route::get('update-managers/{filename?}', [\App\Http\Controllers\ImportController::class, 'managers']);
Route::get('update-catalog/{filename?}', [\App\Http\Controllers\ImportController::class, 'products']);

Route::prefix('/profile')->name('profile.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ProfileController::class, 'index'])->name('index');

    Route::prefix('/orders')->name('orders.')->group(function () {
        Route::get('/current', [\App\Http\Controllers\ProfileController::class, 'orders'])->name('current');
        Route::get('/order-history', [\App\Http\Controllers\ProfileController::class, 'orders'])->name('history');
        Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart');
        Route::get('/success/{random}', [\App\Http\Controllers\CartController::class, 'success'])->name('success');
    });

    Route::post('/{id}', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::post('/change-password/{id}', [\App\Http\Controllers\ProfileController::class, 'changePassword'])->name('change-password');

    Route::prefix('address')->name('address.')->group(function () {
        Route::get('/create', [\App\Http\Controllers\ProfileController::class, 'address'])->name('create');
        Route::get('/edit/{id?}', [\App\Http\Controllers\ProfileController::class, 'address'])->name('edit');
        Route::get('/autocomplete', [\App\Http\Controllers\ProfileController::class, 'address'])->name('autocomplete');
        Route::get('/city', [\App\Http\Controllers\ProfileController::class, 'address'])->name('city');
        Route::put('/store', [\App\Http\Controllers\ProfileController::class, 'address'])->name('store');
        Route::patch('/update/{id}', [\App\Http\Controllers\ProfileController::class, 'address'])->name('update');
        Route::post('/delete/{id?}', [\App\Http\Controllers\ProfileController::class, 'address'])->name('delete');
        Route::post('/change/{id?}', [\App\Http\Controllers\ProfileController::class, 'address'])->name('change');
    });

});
Route::get('/reorder/{id}', [\App\Http\Controllers\ProfileController::class, 'reOrders']);

Route::get('/{page_slug}', [\App\Http\Controllers\PagesController::class, 'index']);
Route::get('/our-life/{id}', [\App\Http\Controllers\PagesController::class, 'index']);
