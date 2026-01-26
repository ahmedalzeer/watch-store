<?php

namespace App\Repositories\Vendor\Variant;

interface VariantRepositoryInterface
{
    public function paginateByStore(int $storeId, array $filters = [], int $perPage = 10);
    public function create(array $data);
    public function update($variant, array $data);
    public function delete($variant);
    public function findById(int $id);
    public function findBySku(string $sku, int $storeId);
}
