<?php

namespace App\Services\Vendor;

use App\Models\ProductVariant;
use App\Models\Product;
use App\Repositories\Vendor\Variant\VariantRepositoryInterface;
use Illuminate\Support\Str;

class VariantService
{
    protected $variantRepository;

    public function __construct(VariantRepositoryInterface $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }

    /**
     * Get paginated variants for a specific store
     */
    public function paginateVariants(int $storeId, array $filters = [], int $perPage = 10)
    {
        return $this->variantRepository->paginateByStore($storeId, $filters, $perPage);
    }

    /**
     * Store a new variant
     */
    public function store(int $storeId, array $data)
    {
        $product = Product::where('store_id', $storeId)->findOrFail($data['product_id']);

        $variantData = [
            'product_id' => $data['product_id'],
            'sku' => $this->generateSku($storeId, $data['sku'] ?? null),
            'price' => $data['price'],
            'discount_price' => $data['discount_price'] ?? null,
            'stock' => $data['stock'],
            'is_primary' => $data['is_primary'] ?? false,
        ];

        $variant = $this->variantRepository->create($variantData);

        // Sync attribute values
        if (isset($data['attribute_value_ids'])) {
            $variant->attributeValues()->sync($data['attribute_value_ids']);
        }

        // If this is set as primary, unset other primary variants
        if ($variant->is_primary) {
            ProductVariant::where('product_id', $variant->product_id)
                ->where('id', '!=', $variant->id)
                ->update(['is_primary' => false]);
        }

        return $variant;
    }

    /**
     * Update an existing variant
     */
    public function update(int $storeId, ProductVariant $variant, array $data)
    {
        $this->authorizeVariant($storeId, $variant);

        $updateData = [
            'price' => $data['price'],
            'discount_price' => $data['discount_price'] ?? null,
            'stock' => $data['stock'],
            'is_primary' => $data['is_primary'] ?? $variant->is_primary,
        ];

        if (isset($data['sku'])) {
            $updateData['sku'] = $data['sku'];
        }

        $variant = $this->variantRepository->update($variant, $updateData);

        // Sync attribute values
        if (isset($data['attribute_value_ids'])) {
            $variant->attributeValues()->sync($data['attribute_value_ids']);
        }

        // If this is set as primary, unset other primary variants
        if ($variant->is_primary) {
            ProductVariant::where('product_id', $variant->product_id)
                ->where('id', '!=', $variant->id)
                ->update(['is_primary' => false]);
        }

        return $variant;
    }

    /**
     * Delete a variant
     */
    public function delete(int $storeId, ProductVariant $variant)
    {
        $this->authorizeVariant($storeId, $variant);
        
        return $this->variantRepository->delete($variant);
    }

    /**
     * Bulk update variants
     */
    public function bulkUpdate(int $storeId, array $data)
    {
        if (!isset($data['variants']) || !is_array($data['variants'])) {
            return;
        }

        foreach ($data['variants'] as $variantId => $variantData) {
            $variant = ProductVariant::find($variantId);
            
            if ($variant && $this->authorizeVariant($storeId, $variant, false)) {
                $this->variantRepository->update($variant, $variantData);
            }
        }
    }

    /**
     * Toggle variant status (active/inactive)
     */
    public function toggleStatus(int $storeId, ProductVariant $variant)
    {
        $this->authorizeVariant($storeId, $variant);
        
        $variant->update(['is_active' => !$variant->is_active]);
        
        return $variant;
    }

    /**
     * Check if variant belongs to the current store
     */
    private function authorizeVariant(int $storeId, ProductVariant $variant, bool $throwException = true): bool
    {
        $belongsToStore = $variant->product->store_id === $storeId;
        
        if (!$belongsToStore && $throwException) {
            abort(403, 'Unauthorized action.');
        }
        
        return $belongsToStore;
    }

    /**
     * Generate unique SKU for variant
     */
    private function generateSku($storeId, $sku = null)
    {
        if ($sku && !ProductVariant::whereHas('product', function ($query) use ($storeId) {
            return $query->where('store_id', $storeId);
        })->where('sku', $sku)->exists()) {
            return $sku;
        }

        if ($sku) {
            // If provided SKU exists, append random string
            return $sku . '-' . Str::random(4);
        }

        // Generate random SKU
        do {
            $sku = 'VAR-' . strtoupper(Str::random(6));
        } while (ProductVariant::whereHas('product', function ($query) use ($storeId) {
            return $query->where('store_id', $storeId);
        })->where('sku', $sku)->exists());

        return $sku;
    }
}
