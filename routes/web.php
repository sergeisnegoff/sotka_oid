<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\Preorder\PreorderCartController;
use App\Http\Controllers\Preorder\PreorderController;
use App\Http\Controllers\Preorder\PreorderReportsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;


Route::get('/', ['as' => 'jquery.load_more', HomeController::class, 'index'])->name('home');

Route::post('/reset-password', [\App\Http\Controllers\Controller::class, 'resetPassword'])->name('reset-password');

Route::get('/img/{path}/{img}', [ImageController::class, 'show'])->where('path', '.*');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::post('/salesystems', [\App\Http\Controllers\Voyager\SalesystemController::class, 'updateTable'])->name('voyager.salesystems.updateTable');
//    Route::post('/brandsale', [\App\Http\Controllers\Voyager\BrandSaleController::class, 'updateTable'])->name('voyager.brandsales.updateTable');

    Route::post('/users/{id}/updateCategories', [\App\Http\Controllers\ProfileController::class, 'updateTableCategories'])->name('voyager.users-categories.updateTable');
    Route::post('/users/{id}/updateBrands', [\App\Http\Controllers\ProfileController::class, 'updateTableBrands'])->name('voyager.users-brands.updateTable');

});
Route::post('/users/active/{id}', [\App\Http\Controllers\ProfileController::class, 'activeAccount'])->name('voyager.users-active');

//Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{title}', [ProductController::class, 'getProductsCat'])->name('products_parent_cats');
Route::get('/products/{id}/{title}', [ProductController::class, 'getProductsSubCat'])->name('products_cats');

Route::get('/product/{id}', [ProductController::class, 'getProduct'])->name('product');
Route::get('/searchProducts', [ProductController::class, 'searchProducts'])->name('searchProducts');
Route::post('add-to-cart/{id}', [HomeController::class, 'addToCart'])->name('addToCart');
Route::get('add-to-cart/{id}', [HomeController::class, 'addToCart']);
Route::patch('update-cart/{id}', [HomeController::class, 'update']);
Route::delete('remove-from-cart', [HomeController::class, 'remove']);
Route::get('basket/load', [CartController::class, 'loadMini']);
Route::get('/preorder/basket/load', [CartController::class, 'loadPreorderMini']);

Auth::routes();
Route::name('cart.')->prefix('cart')->group(function () {
    Route::post('/delete/{id?}', [CartController::class, 'delete'])->name('delete');
    Route::get('/empty', [CartController::class, 'empty'])->name('empty');
    Route::put('/create', [CartController::class, 'create'])->name('create');
    Route::post('/update-count', [CartController::class, 'updateCount'])->name('updateQty');
    Route::post('reorder/{id?}', [\App\Http\Controllers\ProfileController::class, 'reOrders'])->name('reorder');
    Route::post('/preorder/update-count', [CartController::class, 'updatePreOrderCount'])->name('updatePreOrderQty');
    Route::post('/preorder/update-count/{user}', [CartController::class, 'updatePreOrderCountForUser'])->name('updatePreOrderQtyForUser');
});

Route::get('resend-orders', [CartController::class, 'resendMail']);

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
    Route::prefix('total')->group(function() {
        Route::get('/', [ProfileController::class, 'getGeneralTotal']);
        Route::get('/preorder', [ProfileController::class, 'getPreorderTotal']);
    });
    Route::get('/', [\App\Http\Controllers\ProfileController::class, 'index'])->name('index');

    Route::prefix('/orders')->name('orders.')->group(function () {
        Route::get('/current', [\App\Http\Controllers\ProfileController::class, 'orders'])->name('current');
        Route::get('/order-history', [\App\Http\Controllers\ProfileController::class, 'orders'])->name('history');
        Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart');
        Route::get('/success/{random}', [\App\Http\Controllers\CartController::class, 'success'])->name('success');
        Route::get('/export-pdf/{order}', [ProfileController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-xls/{order}', [ProfileController::class, 'exportXls'])->name('export-xls');
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


Route::group(['prefix' => 'preorders'], function () {
    Route::get('/', [PreorderController::class, 'index']);
    Route::get('/cart/ajax', [PreorderCartController::class, 'cart'])->name('preorder_cart_ajax');
    Route::get('/cart/ajax/{user}', [PreorderCartController::class, 'cart'])->name('preorder_user_cart_ajax');
    Route::get('/cart/empty/{preorder}', [PreorderCartController::class, 'empty'])->name('preorder_empty_cart');
    Route::get('/cart/empty/{preorder}/{user}', [PreorderCartController::class, 'empty'])->name('preorder_empty_user_cart');
    Route::get('/cart', [PreorderCartController::class, 'index']);
    Route::post('/cart', [PreorderCartController::class, 'create'])->name('preorder_create');
    Route::post('/cart/{user}', [PreorderCartController::class, 'create'])->name('preorder_create_for_user');
    Route::get('/history', [PreorderController::class, 'history']);
    Route::post('/upload_file', [PreorderController::class, 'clientUploadPost'])->name('preorder.client-upload-post');
    Route::get('/upload', [PreorderController::class, 'clientUpload']);
    Route::get('/table/{preorder}', [PreorderController::class, 'managerSummaryTable'])->name('preorder.summaryTable');
    Route::get('/{id}', [PreorderController::class, 'category'])->name('preorder_category_page');
    Route::get('/info/{id}', [PreorderController::class, 'page']);
    Route::get('/category/{id}/products', [PreorderController::class, 'products']);
    Route::get('/product/{id}', [PreorderController::class, 'product']);
    Route::get('/product/{id}/remove', [PreorderController::class, 'removeFromCart']);

    Route::post('/add-to-cart', [PreorderController::class, 'addToCart'])->name('preorder.addToCart');

    Route::get('/export-pdf/{preorder}', [ProfileController::class, 'exportPreorderPdf']);
    Route::get('/export-xls/{preorder}', [ProfileController::class, 'exportPreorderXls']);

});

Route::group(['prefix' => 'manager', 'middleware' => 'manager'], function () {
    Route::group(['prefix' => 'clients'], function () {
        Route::get('/', [\App\Http\Controllers\ManagerController::class, 'clients'])->name('manager.clients');
        Route::get('/orders', [\App\Http\Controllers\ManagerController::class, 'clientOrders'])->name('manager.clients.orders');
        Route::get('/preorders', [\App\Http\Controllers\ManagerController::class, 'clientPreorders'])->name('manager.clients.preorders');
        Route::get('{user}/upload_xlsx/', [\App\Http\Controllers\ManagerController::class, 'uploadClientXlsx'])->name('manager.clients.upload_xlsx');
        Route::get('{user}/preorder_cart', [\App\Http\Controllers\ManagerController::class, 'clientPreorderCart'])->name('manager.clients.preorder_cart');
        Route::get('{user}/orders/{order}', [\App\Http\Controllers\ManagerController::class, 'concreteClientOrder'])->name('manager.clients.showOrder');
        Route::get('{user}/orders/', [\App\Http\Controllers\ManagerController::class, 'concreteClientOrders'])->name('manager.clients.showOrders');
        Route::get('{user}/orders_history', [\App\Http\Controllers\ManagerController::class, 'concreteClientOrdersHistory'])->name('manager.clients.showOrdersHistory');
        Route::get('{user}/preorders_history', [\App\Http\Controllers\ManagerController::class, 'concreteClientPreordersHistory'])->name('manager.clients.showPreordersHistory');
        Route::post('{user}/preorder_as', [\App\Http\Controllers\ManagerController::class, 'preorderAsUser']);
    });

    Route::get('/orders', [\App\Http\Controllers\ManagerController::class, 'orders'])->name('manager.orders');
    Route::get('/preorders', [\App\Http\Controllers\ManagerController::class, 'preorders'])->name('manager.preorders');
});
Route::group(['prefix' => 'merch', 'middleware' => 'manager'], function () {
    Route::get('/', [\App\Http\Controllers\MerchController::class, 'home'])->name('merch.home');
    Route::get('/history', [\App\Http\Controllers\MerchController::class, 'history'])->name('merch.history');
    Route::group(['prefix' => 'preorder'], function () {
        Route::get('/{preorder}', [\App\Http\Controllers\MerchController::class, 'showPreorder'])->name('merch.show-preorder');
        Route::get('/{preorder}/lazy', [\App\Http\Controllers\MerchController::class, 'lazyPages'])->name('merch.lazy-pages');
        Route::get('/{preorder}/table', [\App\Http\Controllers\MerchController::class, 'getTable']);
        Route::get('/{preorder}/close', [\App\Http\Controllers\MerchController::class, 'close'])->name('merch.close-preorder');
        Route::get('/{preorder}/unclose', [\App\Http\Controllers\MerchController::class, 'unclose'])->name('merch.unclose-preorder');
    });

    Route::post('/product/{product}', [\App\Http\Controllers\MerchController::class, 'changeQty'])->name('merch.change-qty');

});


Route::get('/reorder/{id}', [\App\Http\Controllers\ProfileController::class, 'reOrders']);

Route::get('/{page_slug}', [\App\Http\Controllers\PagesController::class, 'index']);
Route::get('/our-life/{id}', [\App\Http\Controllers\PagesController::class, 'index']);

Route::get('preorder/reports/export', [PreorderReportsController::class, 'index']);


Route::fallback([\App\Http\Controllers\ErrorController::class, 'error_404']);
