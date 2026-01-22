<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $query = $request->input('q');
        $locale = $request->input('locale', App::getLocale());

        $products = Product::where('name', 'like', "%{$query}%")
            ->with(['thumbnail'])
            ->limit(10)
            ->get(['id', 'slug', 'name']);

        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'slug' => $product->slug,
                'thumbnail' => $product->image_url,
                'name' => $product->name,
            ];
        });

        return response()->json($products);
    }

    public function searchResults(Request $request)
    {
        $query = $request->input('q');
        $locale = $request->input('locale', App::getLocale());

        $products = Product::where('name', 'like', "%{$query}%")
            ->with(['thumbnail'])
            ->paginate(12);

        // Map to existing view format if necessary, or just pass products
        return view('search-results', compact('products', 'query'));
    }
}
