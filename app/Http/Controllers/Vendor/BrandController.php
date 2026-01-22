<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Brand;
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
     * Get the current store ID from request or fallback to first store
     */
    private function getStoreId(Request $request): int
    {
        return $request->store_id ?? auth()->user()->stores()->first()->id;
    }

    /**
     * Check if user owns the store
     */
    private function authorizeStore(int $storeId): void
    {
        if (!auth()->user()->stores()->where('id', $storeId)->exists()) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $storeId = $this->getStoreId($request);
        $this->authorizeStore($storeId);

        $brands = $this->brandService->paginateBrands($storeId, $request->search);

        return inertia('Vendor/Brands/Index', [
            'brands' => $brands,
            'storeId' => (int) $storeId,
        ]);
    }

    public function store(BrandRequest $request)
    {
        $storeId = $this->getStoreId($request);
        $this->authorizeStore($storeId);

        $brand = $this->brandService->store($storeId, $request->validated());

        if ($request->image_path) {
            $this->handleMedia($brand, $request->image_path);
        }

        return redirect()->back();
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $storeId = $this->getStoreId($request);
        $this->authorizeStore($storeId);

        $this->brandService->update($storeId, $brand, $request->validated());

        if ($request->filled('image_path')) {
            $brand->clearMediaCollection('brand_logos');
            $this->handleMedia($brand, $request->image_path);
        }

        return redirect()->back();
    }

    public function destroy(Brand $brand)
    {
        $storeId = auth()->user()->stores()->where('id', $brand->store_id)->first()?->id;

        if (!$storeId) {
            abort(403);
        }

        $this->brandService->delete($storeId, $brand);

        return redirect()->back();
    }

    private function handleMedia(Brand $brand, string $path)
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            $brand->addMedia($fullPath)->toMediaCollection('brand_logos');
        }
    }
}
