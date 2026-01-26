<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontBaseController;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends FrontBaseController
{
    public function index()
    {
        $products = Product::with(['brand', 'category', 'media'])
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return inertia('FrontEnd/Index', [
            'products' => $products,
            'brands'   => Brand::all(),
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'media', 'variants.attributeValues.attribute', 'reviews.user']);

        return inertia('FrontEnd/Products/Show', [
            'product' => $product,
            'relatedProducts' => Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->with(['brand', 'media'])
                ->limit(4)
                ->get()
        ]);
    }

    public function getQuickView(Product $product)
    {
        $product->load(['brand', 'category', 'media', 'variants.attributeValues.attribute']);
        
        return response()->json([
            'product' => $product
        ]);
    }
}
