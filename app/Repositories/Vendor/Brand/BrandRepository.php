<?php

namespace App\Repositories\Vendor\Brand;

use App\Models\Brand;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandRepository implements BrandRepositoryInterface
{
    public function getAllByStore(int $storeId): Collection
    {
        return Brand::where('store_id', $storeId)
            ->with('media')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function paginateByStore(int $storeId, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return Brand::where('store_id', $storeId)
            ->with('media')
            ->when($search, function ($query, $search) {
                $query->where('name->ar', 'like', "%{$search}%")
                    ->orWhere('name->en', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): Brand
    {
        return Brand::findOrFail($id);
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        return $brand;
    }

    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }
}
