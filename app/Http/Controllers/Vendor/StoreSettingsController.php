<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreSettingsController extends Controller
{
    /**
     * Verify that the vendor owns the store
     */
    private function authorizeStore(Store $store): void
    {
        if ($store->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }

    public function edit(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $store = $store->load('currency');
        $currencies = Currency::where('is_active', true)->get();

        return Inertia::render('Vendor/Settings/Edit', [
            'store' => $store,
            'currencies' => $currencies,
        ]);
    }

    public function update(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $validated = $request->validate([
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'subdomain' => 'required|string|max:50|unique:stores,subdomain,' . $store->id,
            'currency_id' => 'required|exists:currencies,id',
            'theme_color' => 'nullable|string|max:7',
            'theme_color_hex' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'primary_language' => 'required|in:ar,en',
            'is_active' => 'boolean',
            // SEO Fields
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'seo_title.ar' => 'nullable|string|max:60',
            'seo_title.en' => 'nullable|string|max:60',
            'seo_description.ar' => 'nullable|string|max:160',
            'seo_description.en' => 'nullable|string|max:160',
            'seo_keywords.ar' => 'nullable|string|max:255',
            'seo_keywords.en' => 'nullable|string|max:255',
            // Business Info
            'business_info.street_address' => 'nullable|string|max:255',
            'business_info.city' => 'nullable|string|max:100',
            'business_info.country' => 'nullable|string|max:100',
            'business_info.phone' => 'nullable|string|max:20',
            'business_info.email' => 'nullable|email|max:255',
        ]);

        $store->update($validated);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $store->clearMediaCollection('store_logos');
            $store->addMediaFromRequest('logo')->toMediaCollection('store_logos');
        }

        return redirect()->back()->with('success', 'Store settings updated successfully');
    }
}
