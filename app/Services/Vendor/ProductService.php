<?php

namespace App\Services\Vendor;

use App\Models\Product;
use App\Models\ProductVariant;
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
            'discount_price' => $data['discount_price'] ?? null,
            'sku' => $this->generateSku($storeId, $data['sku'] ?? null),
            'stock' => $data['stock'] ?? 0,
            'condition' => $data['condition'] ?? 'new',
            'is_active' => $data['is_active'] ?? true,
            'main_menu' => $data['main_menu'] ?? false,
            'main_store' => $data['main_store'] ?? false,
            'specifications' => $data['specifications'] ?? [],
            'slug' => $data['slug'] ?? $this->generateSlug($data['name']['en'] ?? $data['name']['ar']),
        ];

        $product = $this->productRepository->create($productData);

        if (isset($data['variants'])) {
            $this->syncVariants($product, $data['variants']);
        }

        return $product;
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
            'discount_price' => $data['discount_price'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
            'main_menu' => $data['main_menu'] ?? false,
            'main_store' => $data['main_store'] ?? false,
            'specifications' => $data['specifications'] ?? [],
            'condition' => $data['condition'] ?? $product->condition,
        ];

        if (isset($data['slug'])) {
            $updateData['slug'] = $data['slug'];
        }

        $product = $this->productRepository->update($product, $updateData);

        if (isset($data['variants'])) {
            $this->syncVariants($product, $data['variants']);
        }

        return $product;
    }

    /**
     * Sync variations for a product
     */
    public function syncVariants(Product $product, array $variants)
    {
        $existingVariantIds = $product->variants()->pluck('id')->toArray();
        $receivedVariantIds = collect($variants)->pluck('id')->filter()->toArray();

        // Delete variants that are not in the new list
        $toDelete = array_diff($existingVariantIds, $receivedVariantIds);
        if (!empty($toDelete)) {
            ProductVariant::whereIn('id', $toDelete)->forceDelete();
        }

        $totalVariantStock = 0;

        foreach ($variants as $index => $variantData) {
            $variant = null;
            if (isset($variantData['id'])) {
                $variant = ProductVariant::find($variantData['id']);
            }

            $variantStock = (int) ($variantData['stock'] ?? 0);
            $totalVariantStock += $variantStock;

            $updateOrNewData = [
                'product_id' => $product->id,
                'sku' => $variantData['sku'] ?? $product->sku . '-' . ($index + 1),
                'price' => $variantData['price'] ?? $product->price,
                'discount_price' => $variantData['discount_price'] ?? null,
                'stock' => $variantStock,
                'is_primary' => $index === 0,
                'is_active' => $variantData['is_active'] ?? true,
            ];

            if ($variant) {
                $variant->update($updateOrNewData);
            } else {
                $variant = ProductVariant::create($updateOrNewData);
            }

            // Sync attribute values
            if (isset($variantData['attribute_value_ids'])) {
                $variant->attributeValues()->sync($variantData['attribute_value_ids']);
            }
        }

        // Update product stock to be total of all variant stocks
        if (count($variants) > 0) {
            $product->update(['stock' => $totalVariantStock]);
        }
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
