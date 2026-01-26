<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VariantRequest;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Store;
use App\Services\Vendor\VariantService;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    protected $variantService;

    public function __construct(VariantService $variantService)
    {
        $this->variantService = $variantService;
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
     * Verify that the variant belongs to the store (via product)
     */
    private function authorizeVariant(ProductVariant $variant, Store $store): void
    {
        if ($variant->product->store_id !== $store->id) {
            abort(404);
        }
    }

    public function index(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $variants = $this->variantService->paginateVariants($store->id, $request->only(['search', 'product']));

        return inertia('Vendor/Variants/Index', [
            'variants' => $variants,
            'products' => Product::where('store_id', $store->id)->select('id', 'name')->get(),
            'store' => $store,
            'filters' => $request->only(['search', 'product'])
        ]);
    }

    public function create(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        return inertia('Vendor/Variants/Create', [
            'store' => $store,
            'products' => Product::where('store_id', $store->id)->select('id', 'name')->get(),
        ]);
    }

    public function store(VariantRequest $request, Store $store)
    {
        $this->authorizeStore($store);

        $variant = $this->variantService->store($store->id, $request->validated());

        return redirect()->route('vendor.variants.show', ['store' => $store, 'variant' => $variant])
            ->with('success', 'Variant created successfully');
    }

    public function show(Request $request, Store $store, ProductVariant $variant)
    {
        $this->authorizeStore($store);
        $this->authorizeVariant($variant, $store);

        return inertia('Vendor/Variants/Show', [
            'variant' => $variant->load('product'),
            'store' => $store,
        ]);
    }

    public function edit(Request $request, Store $store, ProductVariant $variant)
    {
        $this->authorizeStore($store);
        $this->authorizeVariant($variant, $store);

        return inertia('Vendor/Variants/Edit', [
            'variant' => $variant->load('product'),
            'products' => Product::where('store_id', $store->id)->select('id', 'name')->get(),
            'store' => $store,
        ]);
    }

    public function update(VariantRequest $request, Store $store, ProductVariant $variant)
    {
        $this->authorizeStore($store);
        $this->authorizeVariant($variant, $store);

        $this->variantService->update($store->id, $variant, $request->validated());

        return redirect()->route('vendor.variants.show', ['store' => $store, 'variant' => $variant])
            ->with('success', 'Variant updated successfully');
    }

    public function destroy(Request $request, Store $store, ProductVariant $variant)
    {
        $this->authorizeStore($store);
        $this->authorizeVariant($variant, $store);

        $this->variantService->delete($store->id, $variant);

        return redirect()->route('vendor.variants.index', ['store' => $store])
            ->with('success', 'Variant deleted successfully');
    }

    public function bulkUpdate(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $this->variantService->bulkUpdate($store->id, $request->all());

        return redirect()->back()->with('success', 'Variants updated successfully');
    }

    public function toggleStatus(Request $request, Store $store, ProductVariant $variant)
    {
        $this->authorizeStore($store);
        $this->authorizeVariant($variant, $store);

        $this->variantService->toggleStatus($store->id, $variant);

        return redirect()->back()->with('success', 'Variant status updated');
    }
}
