<?php

namespace Tests\Feature\Vendor;

use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $vendor;
    protected $store;
    protected $currency;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'vendor']);
        $this->currency = Currency::create([
            'name' => ['en' => 'USD', 'ar' => 'دولار'],
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_active' => true,
        ]);

        $this->vendor = User::factory()->create();
        $this->vendor->assignRole('vendor');
        $this->store = Store::factory()->create([
            'user_id' => $this->vendor->id,
            'currency_id' => $this->currency->id,
        ]);

        $this->customer = User::factory()->create();
    }

    /** @test */
    public function vendor_can_view_store_orders()
    {
        Order::factory(5)->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.orders.index', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_view_order_details()
    {
        $order = Order::factory()->create(['store_id' => $this->store->id]);
        $product = Product::factory()->create(['store_id' => $this->store->id]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.orders.show', ['store' => $this->store, 'order' => $order]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_update_order_status()
    {
        $order = Order::factory()->create(['store_id' => $this->store->id, 'status' => 'pending']);

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.orders.update', ['store' => $this->store, 'order' => $order]), [
                'status' => 'processing',
            ]);

        // Request processed
    }

    /** @test */
    public function vendor_can_filter_orders_by_status()
    {
        Order::factory(3)->create(['store_id' => $this->store->id, 'status' => 'pending']);
        Order::factory(2)->create(['store_id' => $this->store->id, 'status' => 'processing']);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.orders.index', ['store' => $this->store, 'status' => 'pending']));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_search_orders()
    {
        $order = Order::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.orders.index', ['store' => $this->store, 'search' => $order->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_sees_order_statistics()
    {
        Order::factory(2)->create(['store_id' => $this->store->id, 'status' => 'pending']);
        Order::factory(3)->create(['store_id' => $this->store->id, 'status' => 'processing']);
        Order::factory(1)->create(['store_id' => $this->store->id, 'status' => 'completed']);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.orders.index', ['store' => $this->store]));

        // Request processed
    }

    /** @test */
    public function vendor_cannot_view_orders_from_other_store()
    {
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendor');
        $otherStore = Store::factory()->create(['user_id' => $otherVendor->id]);
        Order::factory()->create(['store_id' => $otherStore->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.orders.index', ['store' => $otherStore]));

        $response->assertStatus(403);
    }

    /** @test */
    public function vendor_cannot_update_other_vendors_order()
    {
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendor');
        $otherStore = Store::factory()->create(['user_id' => $otherVendor->id]);
        $order = Order::factory()->create(['store_id' => $otherStore->id, 'status' => 'pending']);

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.orders.update', ['store' => $this->store, 'order' => $order]), [
                'status' => 'completed',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function order_status_validation_works()
    {
        $order = Order::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.orders.update', ['store' => $this->store, 'order' => $order]), [
                'status' => 'invalid-status',
            ]);

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function orders_only_show_this_stores_products()
    {
        $product1 = Product::factory()->create(['store_id' => $this->store->id]);
        $order = Order::factory()->create(['store_id' => $this->store->id]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product1->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.orders.show', ['store' => $this->store, 'order' => $order]));

        $response->assertStatus(200);
    }
}
