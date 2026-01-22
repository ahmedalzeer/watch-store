<?php

namespace App\Repositories\Vendor\Category;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function getAllByStore(int $storeId): Collection;

    public function paginateByStore(int $storeId, ?string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function getParentCategories(int $storeId): Collection;

    public function findById(int $id): Category;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): bool;
}
