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
        $products = Product::with(['brand', 'category'])
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return inertia('FrontEnd/Index', [
            'products' => $products,
            'brands'   => Brand::all(),
        ]);
    }
}
