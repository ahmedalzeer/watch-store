<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\VendorBaseController;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\Vendor\BrandRequest;

class BrandController extends VendorBaseController
{
    public function index(Request $request)
    {
        $storeId = $request->store_id ?? auth()->user()->stores()->first()->id;

        $brands = Brand::where('store_id', $storeId)
            ->when($request->search, function ($query, $search) {
                $query->where('name->ar', 'like', "%{$search}%")
                    ->orWhere('name->en', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Vendor/Brands/Index', [
            'brands' => $brands,
            'storeId' => (int) $storeId,
        ]);
    }

    public function store(BrandRequest $request) 
    {
        $brand = Brand::create($request->validated());

        if ($request->image_path) {
            $this->handleMedia($brand, $request->image_path);
        }

        return redirect()->back();
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $brand->update($request->validated());

        if ($request->filled('image_path')) {
            $brand->clearMediaCollection('brand_logos');
            $this->handleMedia($brand, $request->image_path);
        }

        return redirect()->back();
    }

    public function destroy(Brand $brand)
    {
        if (!auth()->user()->stores()->where('id', $brand->store_id)->exists()) abort(403);
        $brand->delete();
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
