<?php

namespace App\Services\Vendor;

use App\Models\Product;
use App\Repositories\Vendor\Product\ProductRepositoryInterface;
use Illuminate\Support\Str;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get paginated products for a specific store
     */
    public function paginateProducts(int $storeId, array $filters = [], int $perPage = 10)
    {
        return $this->productRepository->paginateByStore($storeId, $filters, $perPage);
    }

    /**
     * Store a new product
     */
    public function store(int $storeId, array $data)
    {
        $productData = [
            'store_id' => $storeId,
            'name' => [
                'ar' => $data['name']['ar'],
                'en' => $data['name']['en'],
            ],
            'description' => [
                'ar' => $data['description']['ar'] ?? null,
                'en' => $data['description']['en'] ?? null,
            ],
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'] ?? null,
            'price' => $data['price'],
            'compare_price' => $data['compare_price'] ?? null,
            'cost_price' => $data['cost_price'] ?? null,
            'sku' => $this->generateSku($storeId, $data['sku'] ?? null),
            'barcode' => $data['barcode'] ?? null,
            'track_quantity' => $data['track_quantity'] ?? true,
            'quantity' => $data['quantity'] ?? 0,
            'status' => $data['status'] ?? true,
            'is_featured' => $data['is_featured'] ?? false,
            'slug' => $this->generateSlug($data['name']['en'] ?? $data['name']['ar']),
        ];

        return $this->productRepository->create($productData);
    }

    /**
     * Update an existing product
     */
    public function update(int $storeId, Product $product, array $data)
    {
        if (!$this->isAuthorized($storeId, $product)) {
            abort(403, 'Unauthorized action.');
        }

        $updateData = [
            'name' => [
                'ar' => $data['name']['ar'],
                'en' => $data['name']['en'],
            ],
            'description' => [
                'ar' => $data['description']['ar'] ?? null,
                'en' => $data['description']['en'] ?? null,
            ],
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'] ?? null,
            'price' => $data['price'],
            'compare_price' => $data['compare_price'] ?? null,
            'cost_price' => $data['cost_price'] ?? null,
            'barcode' => $data['barcode'] ?? null,
            'track_quantity' => $data['track_quantity'] ?? true,
            'quantity' => $data['quantity'] ?? 0,
            'status' => $data['status'] ?? true,
            'is_featured' => $data['is_featured'] ?? false,
        ];

        // Only update slug/sku if needed, logic can be added here
        // For now, keeping slug/sku updates restricted or manual if needed to avoid breaking links

        return $this->productRepository->update($product, $updateData);
    }

    /**
     * Delete a product
     */
    public function delete(int $storeId, Product $product)
    {
        if (!$this->isAuthorized($storeId, $product)) {
            abort(403);
        }

        $product->clearMediaCollection('product_gallery');
        return $this->productRepository->delete($product);
    }

    /**
     * Check if product belongs to the current store
     */
    private function isAuthorized(int $storeId, Product $product): bool
    {
        return $product->store_id === $storeId;
    }

    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    private function generateSku($storeId, $sku = null)
    {
        if ($sku && !Product::where('store_id', $storeId)->where('sku', $sku)->exists()) {
            return $sku;
        }

        if ($sku) {
             // If provided SKU exists, append random string
             return $sku . '-' . Str::random(4);
        }

        // Generate random SKU
        do {
            $sku = strtoupper(Str::random(8));
        } while (Product::where('store_id', $storeId)->where('sku', $sku)->exists());

        return $sku;
    }
}
