<?php

use App\Http\Controllers\Admin\MediaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\CategoryController;
use App\Http\Controllers\Vendor\BannerController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\BrandController;
use App\Http\Controllers\Vendor\StoreSettingsController;
use App\Http\Controllers\Vendor\OrderController;

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);

    Route::patch('products/{product}/set-main-image/{media}', [ProductController::class, 'setMainImage'])
        ->name('products.set-main-image');

    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('banners', BannerController::class);
    Route::post('banners/data', [BannerController::class, 'getData'])->name('banners.data');
    Route::put('/banners/toggle-status/{id}', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
    Route::post('/banners/update-status', [BannerController::class, 'updateStatus'])->name('banners.updateStatus');

    Route::get('settings', [StoreSettingsController::class, 'edit'])->name('settings.edit');

    Route::post('media/upload/temp', [MediaController::class, 'uploadTemp'])->name('media.upload.temp');
});
