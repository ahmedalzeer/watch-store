<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\VendorBaseController;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\Vendor\CategoryRequest;

class CategoryController extends VendorBaseController
{
    public function index(Request $request)
    {
        $firstStore = auth()->user()->stores()->first();
        $storeId = $request->store_id ?? ($firstStore ? $firstStore->id : null);

        if (!$storeId) {
            return redirect()->route('vendor.dashboard')->with('error', 'No store found.');
        }

        $categories = Category::where('store_id', $storeId)
            ->with('parent')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name->ar', 'like', "%{$search}%")
                      ->orWhere('name->en', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Vendor/Categories/Index', [
            'categories' => $categories,
            'parentCategories' => Category::where('store_id', $storeId)->whereNull('parent_id')->get(),
            'storeId' => (int) $storeId,
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        if ($request->image_path) {
            $this->handleMedia($category, $request->image_path);
        }

        return redirect()->back();
    }

    public function update(CategoryRequest $request, Category $category)
    {
        if ($category->store_id != $request->store_id) {
            abort(403);
        }

        $category->update($request->validated());

        if ($request->filled('image_path')) {
            $category->clearMediaCollection('category_icons');
            $this->handleMedia($category, $request->image_path);
        }

        return redirect()->back();
    }

    public function destroy(Category $category)
    {
        if (!auth()->user()->stores()->where('id', $category->store_id)->exists()) {
            abort(403);
        }

        if ($category->children()->exists()) {
            return redirect()->back()->withErrors(['error' => 'cannot_delete_has_children']);
        }

        $category->clearMediaCollection('category_icons');
        $category->delete();

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
