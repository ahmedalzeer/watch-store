<?php

namespace App\Repositories\Vendor\Category;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllByStore(int $storeId): Collection
    {
        return Category::where('store_id', $storeId)
            ->with(['parent', 'media'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function paginateByStore(int $storeId, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return Category::where('store_id', $storeId)
            ->with(['parent', 'media'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name->ar', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getParentCategories(int $storeId): Collection
    {
        return Category::where('store_id', $storeId)
            ->whereNull('parent_id')
            ->get();
    }

    public function findById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
