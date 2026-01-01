<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\Vendor\CategoryRequest;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $data['store_id'] = auth()->user()->store->id;

        $category = Category::create($data);

        if ($request->image_path) {
            $fullPath = storage_path('app/public/' . $request->image_path);
            if (file_exists($fullPath)) {
                $category->addMedia($fullPath)->toMediaCollection('category_icons');
            }
        }

        return redirect()->back();
    }

    public function update(CategoryRequest $request, Category $category)
    {
        if ($category->store_id !== auth()->user()->store->id) abort(403);

        $category->update($request->validated());

        if ($request->filled('image_path')) {
            $fullPath = storage_path('app/public/' . $request->image_path);
            if (file_exists($fullPath)) {
                $category->clearMediaCollection('category_icons');
                $category->addMedia($fullPath)->toMediaCollection('category_icons');
            }
        }

        return redirect()->back();
    }
}
