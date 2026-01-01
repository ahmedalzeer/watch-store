<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\CategoryController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\BrandController;
use App\Http\Controllers\Vendor\StoreSettingsController;
use App\Http\Controllers\Vendor\OrderController;

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('orders', OrderController::class);

    Route::get('settings', [StoreSettingsController::class, 'edit'])->name('settings.edit');
});
