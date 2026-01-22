<?php

namespace App\Services\Vendor;

use App\Models\Category;
use App\Repositories\Vendor\Category\CategoryRepositoryInterface;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories for a specific store
     */
    public function getAllCategories(int $storeId)
    {
        return $this->categoryRepository->getAllByStore($storeId);
    }

    /**
     * Get paginated categories for a specific store
     */
    public function paginateCategories(int $storeId, ?string $search = null, int $perPage = 10)
    {
        return $this->categoryRepository->paginateByStore($storeId, $search, $perPage);
    }

    /**
     * Get parent categories for dropdown
     */
    public function getParentCategories(int $storeId)
    {
        return $this->categoryRepository->getParentCategories($storeId);
    }

    /**
     * Store a new category
     */
    public function store(int $storeId, array $data)
    {
        $categoryData = [
            'store_id' => $storeId,
            'name' => [
                'ar' => $data['name']['ar'],
                'en' => $data['name']['en'],
            ],
            'slug' => $data['slug'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'main_menu' => $data['main_menu'] ?? false,
            'main_store' => $data['main_store'] ?? false,
        ];

        return $this->categoryRepository->create($categoryData);
    }

    /**
     * Update an existing category
     */
    public function update(int $storeId, Category $category, array $data)
    {
        if (!$this->isAuthorized($storeId, $category)) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent category from being its own parent
        if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
            throw new \InvalidArgumentException('Category cannot be its own parent.');
        }

        $updateData = [
            'name' => $data['name'],
            'slug' => $data['slug'] ?? $category->slug,
            'parent_id' => $data['parent_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'main_menu' => $data['main_menu'] ?? false,
            'main_store' => $data['main_store'] ?? false,
        ];

        return $this->categoryRepository->update($category, $updateData);
    }

    /**
     * Delete a category
     */
    public function delete(int $storeId, Category $category)
    {
        if (!$this->isAuthorized($storeId, $category)) {
            abort(403);
        }

        // Check if category has children
        if ($category->children()->exists()) {
            throw new \Exception('Cannot delete category with children.');
        }

        $category->clearMediaCollection('category_icons');
        return $this->categoryRepository->delete($category);
    }

    /**
     * Check if category belongs to the current store
     */
    private function isAuthorized(int $storeId, Category $category): bool
    {
        return $category->store_id === $storeId;
    }
}
