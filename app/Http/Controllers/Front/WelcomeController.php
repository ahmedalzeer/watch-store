<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontBaseController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Store;
use Illuminate\Http\Request;

class WelcomeController extends FrontBaseController
{
    public function index(Request $request)
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        $currentStore = Store::where('subdomain', $subdomain)->first();

        if (!$currentStore) {
            // Fallback for local development or main domain
            $currentStore = Store::first();
        }

        if (!$currentStore) {
             return inertia('FrontEnd/Welcome', [
                'banners' => [],
                'products' => ['data' => []],
                'brands' => [],
                'categories' => []
            ]);
        }

        $storeId = $currentStore->id;

        $banners = Banner::where('store_id', $storeId)
            ->where('active', true)
            ->orderBy('order')
            ->get()
            ->map(function($banner) {
                return [
                    'title' => $banner->title,
                    'description' => $banner->description,
                    'image_url' => $banner->getFirstMediaUrl('banners') ?: '/images/placeholder-banner.jpg',
                    'link' => $banner->link
                ];
            });

        $products = Product::where('store_id', $storeId)
            ->with(['brand', 'media', 'category'])
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        $brands = Brand::where('store_id', $storeId)
            ->where('is_active', true)
            ->get();
            
        $categories = Category::where('store_id', $storeId)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        return inertia('FrontEnd/Welcome', [
            'banners' => $banners,
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'currentStore' => $currentStore
        ]);
    }
}
