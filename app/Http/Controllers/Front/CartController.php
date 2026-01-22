<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\AttributeValue;
use App\Services\Store\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function addToCart(Request $request)
    {
        $productId = $request->product_id;
        $quantity = (int) ($request->quantity ?? 1);
        $attributeValueIds = $request->attribute_value_ids ?? [];

        $product = Product::with(['thumbnail', 'primaryVariant'])->findOrFail($productId);

        if (!$product->is_active) {
            return response()->json(['message' => 'Product is not available.'], 422);
        }

        $variant = null;
        if (!empty($attributeValueIds)) {
            $variant = $this->matchVariant($productId, $attributeValueIds);
        } else {
            // Fallback to primary variant or first variant
            $variant = $product->primaryVariant ?: $product->variants()->first();
        }

        // If still no variant, but we have a price on product, we could create a dummy one or handle it.
        // But with our schema, variants are expected.
        if (!$variant && $product->price) {
            // Fallback for simple products if no variants were created yet
            $price = $product->discount_price ?? $product->price;
            $name = $product->name;
            $image = $product->image_url;
            $variantId = null;
        } elseif ($variant) {
            $price = $variant->discount_price ?? $variant->price ?? ($product->discount_price ?? $product->price);
            $name = $product->name . ($variant->sku ? " ({$variant->sku})" : "");
            $image = $product->image_url; // We could add variant-specific images later
            $variantId = $variant->id;
        } else {
            return response()->json(['message' => 'Selected variant is not available.'], 422);
        }

        $cart = Session::get('cart', []);
        
        // Key includes variant ID if present to separate different variations
        $key = "cart_{$productId}" . ($variantId ? "_{$variantId}" : "");

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $image,
                'attributes' => $this->getAttributeNames($attributeValueIds),
            ];
        }

        Session::put('cart', $cart);
        Session::put('cart_count', array_sum(array_column($cart, 'quantity')));

        return response()->json([
            'message' => 'Product added to cart successfully.',
            'cart' => $cart,
            'cart_count' => Session::get('cart_count'),
        ]);
    }

    private function matchVariant($productId, array $attributeValueIds)
    {
        $variants = ProductVariant::with('attributeValues')
            ->where('product_id', $productId)
            ->get();

        foreach ($variants as $variant) {
            $variantAttrIds = $variant->attributeValues->pluck('id')->sort()->values()->toArray();
            $targetAttrIds = collect($attributeValueIds)->sort()->values()->toArray();
            
            if ($variantAttrIds === $targetAttrIds) {
                return $variant;
            }
        }

        return null;
    }

    private function getAttributeNames(array $attributeValueIds)
    {
        if (empty($attributeValueIds)) return [];

        return AttributeValue::with('attribute')
            ->whereIn('id', $attributeValueIds)
            ->get()
            ->mapWithKeys(function ($val) {
                return [$val->attribute->name => $val->value];
            })->toArray();
    }

    public function updateCart(Request $request)
    {
        $cart = Session::get('cart', []);

        if ($request->has('cart')) {
            foreach ($request->cart as $item) {
                $productId = $item['product_id'];
                $variantId = $item['variant_id'] ?? null;
                $key = "cart_{$productId}" . ($variantId ? "_{$variantId}" : "");
                
                if (isset($cart[$key])) {
                    $cart[$key]['quantity'] = max(1, intval($item['quantity']));
                }
            }
        }

        Session::put('cart', $cart);
        Session::put('cart_count', array_sum(array_column($cart, 'quantity')));

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'cart' => $cart,
        ]);
    }

    public function viewCart()
    {
        $cart = Session::get('cart', []);
        return Inertia::render('FrontEnd/Cart', [
            'cart' => $cart
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $cart = Session::get('cart', []);
        $productId = $request->product_id;
        $variantId = $request->variant_id ?? null;
        $key = "cart_{$productId}" . ($variantId ? "_{$variantId}" : "");

        if (isset($cart[$key])) {
            unset($cart[$key]);
            Session::put('cart', $cart);
        }

        if (empty($cart)) {
            session()->forget('cart_coupon');
        }

        return response()->json([
            'message' => 'Product removed from cart.',
            'cart' => $cart,
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $result = $this->cartService->applyCoupon($request->code);
        return response()->json($result);
    }

    public function removeCoupon()
    {
        $this->cartService->removeCoupon();
        return response()->json(['success' => true, 'message' => 'Coupon removed successfully!']);
    }
}
