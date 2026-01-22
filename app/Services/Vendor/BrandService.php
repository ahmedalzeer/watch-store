<?php

namespace App\Services\Vendor;

use App\Models\Brand;
use App\Repositories\Vendor\Brand\BrandRepositoryInterface;

class BrandService
{
    protected $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Get all brands for a specific store
     */
    public function getAllBrands(int $storeId)
    {
        return $this->brandRepository->getAllByStore($storeId);
    }

    /**
     * Get paginated brands for a specific store
     */
    public function paginateBrands(int $storeId, ?string $search = null, int $perPage = 10)
    {
        return $this->brandRepository->paginateByStore($storeId, $search, $perPage);
    }

    /**
     * Store a new brand
     */
    public function store(int $storeId, array $data)
    {
        $brandData = [
            'store_id' => $storeId,
            'name' => [
                'ar' => $data['name']['ar'],
                'en' => $data['name']['en'],
            ],
            'slug' => $data['slug'] ?? null,
            'website' => $data['website'] ?? null,
            'is_featured' => $data['is_featured'] ?? false,
            'is_active' => $data['is_active'] ?? true,
            'main_menu' => $data['main_menu'] ?? false,
            'main_store' => $data['main_store'] ?? false,
        ];

        return $this->brandRepository->create($brandData);
    }

    /**
     * Update an existing brand
     */
    public function update(int $storeId, Brand $brand, array $data)
    {
        if (!$this->isAuthorized($storeId, $brand)) {
            abort(403, 'Unauthorized action.');
        }

        $updateData = [
            'name' => $data['name'],
            'slug' => $data['slug'] ?? $brand->slug,
            'website' => $data['website'] ?? null,
            'is_featured' => $data['is_featured'] ?? false,
            'is_active' => $data['is_active'] ?? true,
            'main_menu' => $data['main_menu'] ?? false,
            'main_store' => $data['main_store'] ?? false,
        ];

        return $this->brandRepository->update($brand, $updateData);
    }

    /**
     * Delete a brand
     */
    public function delete(int $storeId, Brand $brand)
    {
        if (!$this->isAuthorized($storeId, $brand)) {
            abort(403);
        }

        $brand->clearMediaCollection('brand_logos');
        return $this->brandRepository->delete($brand);
    }

    /**
     * Check if brand belongs to the current store
     */
    private function isAuthorized(int $storeId, Brand $brand): bool
    {
        return $brand->store_id === $storeId;
    }
}
