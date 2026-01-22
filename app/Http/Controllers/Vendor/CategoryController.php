<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
     * Get the current store ID from request or fallback to first store
     */
    private function getStoreId(Request $request): ?int
    {
        $firstStore = auth()->user()->stores()->first();
        return $request->store_id ?? ($firstStore ? $firstStore->id : null);
    }

    /**
     * Check if user owns the store
     */
    private function authorizeStore(int $storeId): void
    {
        if (!auth()->user()->stores()->where('id', $storeId)->exists()) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $storeId = $this->getStoreId($request);
        $this->authorizeStore($storeId); // Enforce authorization

        if (!$storeId) {
            return redirect()->route('vendor.dashboard')->with('error', 'No store found.');
        }

        $categories = $this->categoryService->paginateCategories($storeId, $request->search);
        $parentCategories = $this->categoryService->getParentCategories($storeId);

        return inertia('Vendor/Categories/Index', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
            'storeId' => (int) $storeId,
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $storeId = $this->getStoreId($request);
        $this->authorizeStore($storeId);

        $category = $this->categoryService->store($storeId, $request->validated());

        if ($request->image_path) {
            $this->handleMedia($category, $request->image_path);
        }

        return redirect()->back();
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $storeId = $this->getStoreId($request);
        $this->authorizeStore($storeId);

        try {
            $this->categoryService->update($storeId, $category, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['parent_id' => __('messages.cannot_be_parent_to_self')]);
        }

        if ($request->filled('image_path')) {
            $category->clearMediaCollection('category_icons');
            $this->handleMedia($category, $request->image_path);
        }

        return redirect()->back();
    }

    public function destroy(Category $category)
    {
        $storeId = auth()->user()->stores()->where('id', $category->store_id)->first()?->id;

        if (!$storeId) {
            abort(403);
        }

        try {
            $this->categoryService->delete($storeId, $category);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'cannot_delete_has_children']);
        }

        return redirect()->back();
    }

    private function handleMedia(Category $category, string $path)
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            $category->addMedia($fullPath)->toMediaCollection('category_icons');
        }
    }
}
