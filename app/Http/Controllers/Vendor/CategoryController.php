<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Store;
use App\Services\Vendor\CategoryService;
use App\Http\Requests\Vendor\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Verify that the vendor owns the store
     */
    private function authorizeStore(Store $store): void
    {
        if ($store->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Verify that the category belongs to the store
     */
    private function authorizeCategory(Category $category, Store $store): void
    {
        if ($category->store_id !== $store->id) {
            abort(404);
        }
    }

    public function index(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $categories = $this->categoryService->paginateCategories($store->id, $request->search);
        $parentCategories = $this->categoryService->getParentCategories($store->id);

        return inertia('Vendor/Categories/Index', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
            'store' => $store,
        ]);
    }

    public function create(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        return inertia('Vendor/Categories/Create', [
            'store' => $store,
            'parentCategories' => $this->categoryService->getParentCategories($store->id),
        ]);
    }

    public function store(CategoryRequest $request, Store $store)
    {
        $this->authorizeStore($store);

        $category = $this->categoryService->store($store->id, $request->validated());

        if ($request->image_path) {
            $this->handleMedia($category, $request->image_path);
        }

        return redirect()->route('vendor.categories.show', ['store' => $store, 'category' => $category]);
    }

    public function show(Request $request, Store $store, Category $category)
    {
        $this->authorizeStore($store);
        $this->authorizeCategory($category, $store);

        return inertia('Vendor/Categories/Show', [
            'category' => $category,
            'store' => $store,
        ]);
    }

    public function edit(Request $request, Store $store, Category $category)
    {
        $this->authorizeStore($store);
        $this->authorizeCategory($category, $store);

        return inertia('Vendor/Categories/Edit', [
            'category' => $category,
            'parentCategories' => $this->categoryService->getParentCategories($store->id),
            'store' => $store,
        ]);
    }

    public function update(CategoryRequest $request, Store $store, Category $category)
    {
        $this->authorizeStore($store);
        $this->authorizeCategory($category, $store);

        try {
            $this->categoryService->update($store->id, $category, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['parent_id' => 'Cannot set category as parent to itself']);
        }

        if ($request->filled('image_path')) {
            $category->clearMediaCollection('category_icons');
            $this->handleMedia($category, $request->image_path);
        }

        return redirect()->route('vendor.categories.show', ['store' => $store, 'category' => $category]);
    }

    public function destroy(Request $request, Store $store, Category $category)
    {
        $this->authorizeStore($store);
        $this->authorizeCategory($category, $store);

        try {
            $this->categoryService->delete($store->id, $category);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete category with sub-categories']);
        }

        return redirect()->route('vendor.categories.index', ['store' => $store]);
    }

    private function handleMedia(Category $category, string $path)
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            $category->addMedia($fullPath)->toMediaCollection('category_icons');
        }
    }
}
