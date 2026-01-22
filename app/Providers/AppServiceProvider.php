<?php

namespace App\Providers;

use App\Repositories\Vendor\Banner\BannerRepository;
use App\Repositories\Vendor\Banner\BannerRepositoryInterface;
use App\Repositories\Vendor\Brand\BrandRepository;
use App\Repositories\Vendor\Brand\BrandRepositoryInterface;
use App\Repositories\Vendor\Category\CategoryRepository;
use App\Repositories\Vendor\Category\CategoryRepositoryInterface;
use App\Repositories\Vendor\Product\ProductRepository;
use App\Repositories\Vendor\Product\ProductRepositoryInterface;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Banner Repository
        $this->app->bind(BannerRepositoryInterface::class, BannerRepository::class);

        // Brand Repository
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);

        // Category Repository
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);

        // Product Repository
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
