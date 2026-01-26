<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\Brand;
use App\Services\Vendor\ProductService;
use App\Http\Requests\Vendor\ProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
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
     * Verify that the product belongs to the store
     */
    private function authorizeProduct(Product $product, Store $store): void
    {
        if ($product->store_id !== $store->id) {
            abort(404);
        }
    }

    public function index(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $products = $this->productService->paginateProducts($store->id, $request->only(['search']));

        return inertia('Vendor/Products/Index', [
            'products' => $products,
            'categories' => Category::where('store_id', $store->id)->get(),
            'brands' => Brand::where('store_id', $store->id)->get(),
            'store' => $store,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        return inertia('Vendor/Products/Create', [
            'store' => $store,
            'categories' => Category::where('store_id', $store->id)->get(),
            'brands' => Brand::where('store_id', $store->id)->get(),
        ]);
    }

    public function store(ProductRequest $request, Store $store)
    {
        $this->authorizeStore($store);

        $product = $this->productService->store($store->id, $request->validated());

        if ($request->has('image_paths')) {
            foreach ($request->image_paths as $path) {
                $this->handleMedia($product, $path, 'product_gallery', $path === $request->main_image_path);
            }
        }

        return redirect()->route('vendor.products.show', ['store' => $store, 'product' => $product]);
    }

    public function show(Request $request, Store $store, Product $product)
    {
        $this->authorizeStore($store);
        $this->authorizeProduct($product, $store);

        return inertia('Vendor/Products/Show', [
            'product' => $product->load('variants', 'reviews'),
            'store' => $store,
        ]);
    }

    public function edit(Request $request, Store $store, Product $product)
    {
        $this->authorizeStore($store);
        $this->authorizeProduct($product, $store);

        return inertia('Vendor/Products/Edit', [
            'product' => $product->load('variants'),
            'categories' => Category::where('store_id', $store->id)->get(),
            'brands' => Brand::where('store_id', $store->id)->get(),
            'store' => $store,
        ]);
    }

    public function update(ProductRequest $request, Store $store, Product $product)
    {
        $this->authorizeStore($store);
        $this->authorizeProduct($product, $store);

        if ($request->has('update_specs_only')) {
            $product->update([
                'specifications' => $request->specifications
            ]);
            return redirect()->route('vendor.products.edit', ['store' => $store, 'product' => $product]);
        }

        $this->productService->update($store->id, $product, $request->validated());

        if ($request->filled('image_paths')) {
            foreach ($request->image_paths as $path) {
                $this->handleMedia($product, $path, 'product_gallery');
            }
        }

        return redirect()->route('vendor.products.show', ['store' => $store, 'product' => $product]);
    }

    public function setMainImage(Request $request, Store $store, Product $product, $mediaId)
    {
        $this->authorizeStore($store);
        $this->authorizeProduct($product, $store);

        $product->getMedia('product_gallery')->each(function ($media) {
            $media->setCustomProperty('is_main', false);
            $media->save();
        });

        $media = $product->media()->find($mediaId);
        if ($media) {
            $media->setCustomProperty('is_main', true);
            $media->save();
        }

        return back();
    }

    public function destroy(Request $request, Store $store, Product $product)
    {
        $this->authorizeStore($store);
        $this->authorizeProduct($product, $store);

        $this->productService->delete($store->id, $product);

        return redirect()->route('vendor.products.index', ['store' => $store]);
    }

    private function handleMedia(Product $product, string $path, string $collection, bool $isMain = false)
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            $media = $product->addMedia($fullPath)->toMediaCollection($collection);
            if ($isMain) {
                $media->setCustomProperty('is_main', true);
                $media->save();
            }
        }
    }
}
