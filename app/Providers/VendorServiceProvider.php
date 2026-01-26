<?php

namespace App\Providers;

use App\Repositories\Vendor\Variant\VariantRepository;
use App\Repositories\Vendor\Variant\VariantRepositoryInterface;
use App\Services\Vendor\VariantService;
use Illuminate\Support\ServiceProvider;

class VendorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Variant Repository
        $this->app->bind(VariantRepositoryInterface::class, VariantRepository::class);
        
        // Bind Variant Service
        $this->app->singleton(VariantService::class, function ($app) {
            return new VariantService($app->make(VariantRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
