<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Verify that the vendor owns the store
     */
    private function authorizeStore(Store $store): void
    {
        if ($store->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Verify that the order belongs to the store
     */
    private function authorizeOrder(Order $order, Store $store): void
    {
        if ($order->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }
    }

    public function index(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $query = Order::where('store_id', $store->id)
            ->with(['user', 'items.product'])
            ->latest();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Get order statistics
        $stats = [
            'total' => Order::where('store_id', $store->id)->count(),
            'pending' => Order::where('store_id', $store->id)->where('status', 'pending')->count(),
            'processing' => Order::where('store_id', $store->id)->where('status', 'processing')->count(),
            'completed' => Order::where('store_id', $store->id)->where('status', 'completed')->count(),
            'cancelled' => Order::where('store_id', $store->id)->where('status', 'cancelled')->count(),
        ];

        return Inertia::render('Vendor/Orders/Index', [
            'orders' => $orders,
            'stats' => $stats,
            'store' => $store,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function show(Request $request, Store $store, Order $order)
    {
        $this->authorizeStore($store);
        $this->authorizeOrder($order, $store);

        $order->load(['user', 'items.product', 'items.variant']);

        return Inertia::render('Vendor/Orders/Show', [
            'order' => $order,
            'store' => $store,
        ]);
    }

    public function update(Request $request, Store $store, Order $order)
    {
        $this->authorizeStore($store);
        $this->authorizeOrder($order, $store);

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Order status updated successfully');
    }
}
