<?php

use App\Http\Controllers\Admin\MediaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\DashboardAnalyticsController;
use App\Http\Controllers\Vendor\CategoryController;
use App\Http\Controllers\Vendor\BannerController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\BrandController;
use App\Http\Controllers\Vendor\StoreSettingsController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\AttributeController;
use App\Http\Controllers\Vendor\AttributeValueController;
use App\Http\Controllers\Vendor\VariantController;

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stores/{store}', [DashboardController::class, 'selectStore'])->name('store.select');

    // Dashboard Analytics Routes with Store Parameter
    Route::get('/stores/{store}/dashboard', [DashboardAnalyticsController::class, 'getStatistics'])->name('dashboard.analytics');
    Route::get('/stores/{store}/logs', [DashboardAnalyticsController::class, 'getLogs'])->name('dashboard.logs');
    Route::get('/stores/{store}/export', [DashboardAnalyticsController::class, 'exportDatabase'])->name('dashboard.export');
    Route::get('/stores/{store}/migration-status', [DashboardAnalyticsController::class, 'getMigrationStatus'])->name('dashboard.migration-status');

    // Products Resources with Store Parameter
    Route::get('/stores/{store}/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/stores/{store}/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/stores/{store}/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/stores/{store}/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/stores/{store}/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/stores/{store}/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/stores/{store}/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::patch('/stores/{store}/products/{product}/set-main-image/{media}', [ProductController::class, 'setMainImage'])
        ->name('products.set-main-image');

    // Variants Resources with Store Parameter
    Route::get('/stores/{store}/variants', [VariantController::class, 'index'])->name('variants.index');
    Route::get('/stores/{store}/variants/create', [VariantController::class, 'create'])->name('variants.create');
    Route::post('/stores/{store}/variants', [VariantController::class, 'store'])->name('variants.store');
    Route::get('/stores/{store}/variants/{variant}', [VariantController::class, 'show'])->name('variants.show');
    Route::get('/stores/{store}/variants/{variant}/edit', [VariantController::class, 'edit'])->name('variants.edit');
    Route::put('/stores/{store}/variants/{variant}', [VariantController::class, 'update'])->name('variants.update');
    Route::delete('/stores/{store}/variants/{variant}', [VariantController::class, 'destroy'])->name('variants.destroy');
    Route::post('/stores/{store}/variants/bulk-update', [VariantController::class, 'bulkUpdate'])->name('variants.bulk-update');
    Route::post('/stores/{store}/variants/{variant}/toggle-status', [VariantController::class, 'toggleStatus'])->name('variants.toggle-status');

    // Categories, Brands, Attributes with Store Parameter
    Route::get('/stores/{store}/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/stores/{store}/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/stores/{store}/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/stores/{store}/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/stores/{store}/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/stores/{store}/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/stores/{store}/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/stores/{store}/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/stores/{store}/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/stores/{store}/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/stores/{store}/brands/{brand}', [BrandController::class, 'show'])->name('brands.show');
    Route::get('/stores/{store}/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/stores/{store}/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/stores/{store}/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

    Route::get('/stores/{store}/attributes', [AttributeController::class, 'index'])->name('attributes.index');
    Route::post('/stores/{store}/attributes', [AttributeController::class, 'store'])->name('attributes.store');
    Route::put('/stores/{store}/attributes/{attribute}', [AttributeController::class, 'update'])->name('attributes.update');
    Route::delete('/stores/{store}/attributes/{attribute}', [AttributeController::class, 'destroy'])->name('attributes.destroy');

    Route::post('/stores/{store}/attribute-values', [AttributeValueController::class, 'store'])->name('attribute-values.store');
    Route::put('/stores/{store}/attribute-values/{attributeValue}', [AttributeValueController::class, 'update'])->name('attribute-values.update');
    Route::delete('/stores/{store}/attribute-values/{attributeValue}', [AttributeValueController::class, 'destroy'])->name('attribute-values.destroy');

    // Orders with Store Parameter
    Route::get('/stores/{store}/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/stores/{store}/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/stores/{store}/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

    // Banners with Store Parameter
    Route::get('/stores/{store}/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/stores/{store}/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/stores/{store}/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/stores/{store}/banners/{banner}', [BannerController::class, 'show'])->name('banners.show');
    Route::get('/stores/{store}/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('/stores/{store}/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/stores/{store}/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
    Route::post('/stores/{store}/banners/update-status', [BannerController::class, 'updateStatus'])->name('banners.updateStatus');

    // Settings with Store Parameter
    Route::get('/stores/{store}/settings', [StoreSettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/stores/{store}/settings', [StoreSettingsController::class, 'update'])->name('settings.update');

    Route::post('media/upload/temp', [MediaController::class, 'uploadTemp'])->name('media.upload.temp');
});
