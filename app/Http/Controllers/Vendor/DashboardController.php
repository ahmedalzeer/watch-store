<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Show vendor dashboard with store selection
     */
    public function index(Request $request)
    {
        $vendor = auth()->user();
        $stores = $vendor->stores()->get();

        if ($stores->isEmpty()) {
            return Inertia::render('Vendor/NoStores');
        }

        // If vendor has only one store, redirect to that store's dashboard
        if ($stores->count() === 1) {
            return redirect()->route('vendor.store.select', $stores->first()->id);
        }

        // Show store selector
        return Inertia::render('Vendor/Index', [
            'stores' => $stores->map(fn($store) => [
                'id' => $store->id,
                'name' => $store->name,
                'subdomain' => $store->subdomain,
                'is_active' => $store->is_active,
                'products_count' => $store->products()->count(),
                'orders_count' => $store->orders()->count(),
                'revenue' => '0', // Will be calculated from orders if needed
                'logo' => $store->getFirstMediaUrl('store_logos'),
            ]),
        ]);
    }

    /**
     * Select a store and view its dashboard
     */
    public function selectStore(Store $store, Request $request)
    {
        $vendor = auth()->user();

        // Verify vendor owns this store
        if (!$vendor->stores()->where('stores.id', $store->id)->exists()) {
            abort(403, 'Unauthorized store access');
        }

        // Show store dashboard with navigation options
        return Inertia::render('Vendor/Dashboard', [
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'subdomain' => $store->subdomain,
                'is_active' => $store->is_active,
                'products_count' => $store->products()->count(),
                'orders_count' => $store->orders()->count(),
                'revenue' => '0', // Will be calculated from orders if needed
                'logo' => $store->getFirstMediaUrl('store_logos'),
                'currency' => $store->currency,
            ],
        ]);
    }
}
