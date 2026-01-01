<?php

use App\Http\Controllers\Admin\MediaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('stores', StoreController::class);

    Route::post('media/upload/temp', [MediaController::class, 'uploadTemp'])->name('media.upload.temp');
});
