<?php

namespace App\Repositories\Vendor\Variant;

use App\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class VariantRepository implements VariantRepositoryInterface
{
    public function paginateByStore(int $storeId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = ProductVariant::with(['product', 'attributeValues.attribute'])
            ->whereHas('product', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            });

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['product'])) {
            $query->where('product_id', $filters['product']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): ProductVariant
    {
        return ProductVariant::create($data);
    }

    public function update($variant, array $data): ProductVariant
    {
        $variant->update($data);
        return $variant->fresh();
    }

    public function delete($variant): bool
    {
        return $variant->delete();
    }

    public function findById(int $id): ?ProductVariant
    {
        return ProductVariant::with(['product', 'attributeValues.attribute'])->find($id);
    }

    public function findBySku(string $sku, int $storeId): ?ProductVariant
    {
        return ProductVariant::where('sku', $sku)
            ->whereHas('product', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->first();
    }
}
