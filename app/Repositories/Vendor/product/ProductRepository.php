<?php

namespace App\Repositories\Vendor\Product;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllByStore(int $storeId): Collection
    {
        return Product::where('store_id', $storeId)
            ->with(['category', 'brand', 'media'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function paginateByStore(int $storeId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Product::where('store_id', $storeId)
            ->with(['category', 'brand'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name->ar', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}
