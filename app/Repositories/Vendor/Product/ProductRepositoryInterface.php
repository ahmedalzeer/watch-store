<?php

namespace App\Repositories\Vendor\Product;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function getAllByStore(int $storeId): Collection;

    public function paginateByStore(int $storeId, array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findById(int $id): Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): bool;
}
