<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index()
    {
        $customer = Auth::user();

        $products = $customer->wishlistProducts()
            ->with(['thumbnail'])
            ->withCount('reviews')
            ->orderBy('wishlists.created_at', 'desc')
            ->get();

        return Inertia::render('FrontEnd/Wishlist', [
            'products' => $products
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $customer = Auth::user();

        $wishlist = Wishlist::where('user_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            return response()->json(['status' => 'removed', 'message' => __('messages.removed_from_wishlist')]);
        }

        Wishlist::create([
            'user_id' => $customer->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['status' => 'added', 'message' => __('store.product_detail.added_to_wishlist')]);
    }
}
