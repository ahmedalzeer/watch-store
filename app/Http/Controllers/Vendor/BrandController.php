<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Store;
use App\Services\Vendor\BrandService;
use App\Http\Requests\Vendor\BrandRequest;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Verify that the vendor owns the store
     */
    private function authorizeStore(Store $store): void
    {
        if ($store->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Verify that the brand belongs to the store
     */
    private function authorizeBrand(Brand $brand, Store $store): void
    {
        if ($brand->store_id !== $store->id) {
            abort(404);
        }
    }

    public function index(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $brands = $this->brandService->paginateBrands($store->id, $request->search);

        return inertia('Vendor/Brands/Index', [
            'brands' => $brands,
            'store' => $store,
        ]);
    }

    public function create(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        return inertia('Vendor/Brands/Create', [
            'store' => $store,
        ]);
    }

    public function store(BrandRequest $request, Store $store)
    {
        $this->authorizeStore($store);

        $brand = $this->brandService->store($store->id, $request->validated());

        if ($request->image_path) {
            $this->handleMedia($brand, $request->image_path);
        }

        return redirect()->route('vendor.brands.show', ['store' => $store, 'brand' => $brand]);
    }

    public function show(Request $request, Store $store, Brand $brand)
    {
        $this->authorizeStore($store);
        $this->authorizeBrand($brand, $store);

        return inertia('Vendor/Brands/Show', [
            'brand' => $brand,
            'store' => $store,
        ]);
    }

    public function edit(Request $request, Store $store, Brand $brand)
    {
        $this->authorizeStore($store);
        $this->authorizeBrand($brand, $store);

        return inertia('Vendor/Brands/Edit', [
            'brand' => $brand,
            'store' => $store,
        ]);
    }

    public function update(BrandRequest $request, Store $store, Brand $brand)
    {
        $this->authorizeStore($store);
        $this->authorizeBrand($brand, $store);

        $this->brandService->update($store->id, $brand, $request->validated());

        if ($request->filled('image_path')) {
            $brand->clearMediaCollection('brand_logos');
            $this->handleMedia($brand, $request->image_path);
        }

        return redirect()->route('vendor.brands.show', ['store' => $store, 'brand' => $brand]);
    }

    public function destroy(Request $request, Store $store, Brand $brand)
    {
        $this->authorizeStore($store);
        $this->authorizeBrand($brand, $store);

        $this->brandService->delete($store->id, $brand);

        return redirect()->route('vendor.brands.index', ['store' => $store]);
    }

    private function handleMedia(Brand $brand, string $path)
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            $brand->addMedia($fullPath)->toMediaCollection('brand_logos');
        }
    }
}
