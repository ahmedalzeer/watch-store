<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index()
    {
        $paymentGateways = PaymentGateway::where('is_active', 1)->get();

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $shipping = 0; // Can implement shipping logic later
        $total = $subtotal + $shipping;

        return Inertia::render('FrontEnd/Checkout', [
            'cart' => $cart,
            'subtotal' => (float)$subtotal,
            'shipping' => (float)$shipping,
            'total' => (float)$total,
            'paymentGateways' => $paymentGateways,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'payment_method' => 'required|in:stripe,cod',
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Cart is empty!');
        }

        // Calculate total
        $total = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Get Store ID (from first item or default)
        $firstItem = reset($cart);
        $product = Product::find($firstItem['product_id']);
        $storeId = $product->store_id;

        // Save Order
        $order = Order::create([
            'store_id' => $storeId,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'total' => $total,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending', // Both pending initially
            'shipping_details' => $request->only(['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'country']),
        ]);

        // Save Order Items
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // Clear the session cart
        Session::forget('cart');

        if ($request->payment_method === 'stripe') {
            // Redirect to stripe payment process or return client secret
            return redirect()->route('stripe.checkout.process', ['order_id' => $order->id]);
        }

        return redirect()->route('welcome')->with('success', 'Order placed successfully! (Cash on Delivery)');
    }
}
