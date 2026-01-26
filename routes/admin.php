<?php

use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\PermissionManagementController;
use App\Http\Controllers\Admin\AdminDashboardAnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['auth', 'role:admin|super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Dashboard Analytics
    Route::get('analytics', [AdminDashboardAnalyticsController::class, 'getStatistics'])->name('analytics');
    Route::get('analytics/logs', [AdminDashboardAnalyticsController::class, 'getLogs'])->name('analytics.logs');
    Route::get('analytics/export', [AdminDashboardAnalyticsController::class, 'exportDatabase'])->name('analytics.export');

    Route::resource('users', UserController::class);
    Route::resource('stores', StoreController::class);

    Route::post('media/upload/temp', [MediaController::class, 'uploadTemp'])->name('media.upload.temp');

    // Permissions Management (Only Super Admin)
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('permissions', [PermissionManagementController::class, 'index'])->name('permissions.index');
        Route::post('roles/{role}/permissions', [PermissionManagementController::class, 'updateRolePermissions'])->name('roles.permissions.update');
        Route::post('users/{user}/roles', [PermissionManagementController::class, 'updateUserRoles'])->name('users.roles.update');
        Route::post('users/{user}/permissions', [PermissionManagementController::class, 'assignDirectPermissions'])->name('users.permissions.assign');
        Route::post('roles/create', [PermissionManagementController::class, 'createRole'])->name('roles.create');
        Route::delete('roles/{role}', [PermissionManagementController::class, 'deleteRole'])->name('roles.delete');
        Route::get('users/{user}/permissions', [PermissionManagementController::class, 'viewUserPermissions'])->name('users.permissions.view');
        Route::get('permissions/summary', [PermissionManagementController::class, 'permissionsSummary'])->name('permissions.summary');

        // Impersonation Routes
        Route::post('impersonate/{user}', [ImpersonationController::class, 'impersonate'])->name('impersonate.user');
        Route::post('impersonate/stop', [ImpersonationController::class, 'stopImpersonate'])->name('impersonate.stop');
        Route::get('impersonate/status', [ImpersonationController::class, 'getImpersonationStatus'])->name('impersonate.status');
        Route::get('impersonate/users', [ImpersonationController::class, 'listUsers'])->name('impersonate.list');
    });
});
