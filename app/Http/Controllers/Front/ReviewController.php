<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        // Check if already reviewed
        if (ProductReview::where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->exists()) {
            return back()->with('error', __('messages.review_already_submitted'));
        }

        ProductReview::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'review' => $request->review,
            'is_approved' => 1,
        ]);

        return back()->with('success', __('messages.review_success'));
    }
}
