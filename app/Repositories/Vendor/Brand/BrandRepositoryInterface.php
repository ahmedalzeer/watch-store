<?php

namespace App\Repositories\Vendor\Brand;

use App\Models\Brand;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface
{
    public function getAllByStore(int $storeId): Collection;

    public function paginateByStore(int $storeId, ?string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function findById(int $id): Brand;

    public function create(array $data): Brand;

    public function update(Brand $brand, array $data): Brand;

    public function delete(Brand $brand): bool;
}
