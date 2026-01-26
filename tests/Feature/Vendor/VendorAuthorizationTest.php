<?php

namespace Tests\Feature\Vendor;

use App\Models\Category;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $vendor1;
    protected $vendor2;
    protected $store1;
    protected $store2;
    protected $product1;
    protected $currency;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and currency
        Role::create(['name' => 'vendor']);
        $this->currency = Currency::create([
            'name' => ['en' => 'USD', 'ar' => 'دولار'],
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_active' => true,
        ]);

        // Create first vendor with store
        $this->vendor1 = User::factory()->create();
        $this->vendor1->assignRole('vendor');
        $this->store1 = Store::factory()->create([
            'user_id' => $this->vendor1->id,
            'currency_id' => $this->currency->id,
        ]);

        // Create second vendor with store
        $this->vendor2 = User::factory()->create();
        $this->vendor2->assignRole('vendor');
        $this->store2 = Store::factory()->create([
            'user_id' => $this->vendor2->id,
            'currency_id' => $this->currency->id,
        ]);

        // Create product in vendor1's store
        $this->product1 = Product::factory()->create(['store_id' => $this->store1->id]);
    }

    /** @test */
    public function vendor_cannot_access_other_vendors_store()
    {
        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.products.index', ['store' => $this->store2]));

        $response->assertStatus(403);
    }

    /** @test */
    public function vendor_cannot_view_other_vendors_product()
    {
        $product2 = Product::factory()->create(['store_id' => $this->store2->id]);

        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.products.show', ['store' => $this->store1, 'product' => $product2]));

        $response->assertStatus(404);
    }

    /** @test */
    public function vendor_cannot_edit_other_vendors_product()
    {
        $product2 = Product::factory()->create(['store_id' => $this->store2->id]);

        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.products.edit', ['store' => $this->store1, 'product' => $product2]));

        $response->assertStatus(404);
    }

    /** @test */
    public function vendor_cannot_delete_other_vendors_product()
    {
        $product2 = Product::factory()->create(['store_id' => $this->store2->id]);

        $response = $this->actingAs($this->vendor1)
            ->delete(route('vendor.products.destroy', ['store' => $this->store1, 'product' => $product2]));

        $response->assertStatus(404);
    }

    /** @test */
    public function vendor_cannot_update_other_vendors_product()
    {
        $product2 = Product::factory()->create(['store_id' => $this->store2->id]);

        $data = [
            'name' => ['en' => 'Hacked', 'ar' => 'هاك'],
            'price' => 1,
            'stock' => 1,
        ];

        $response = $this->actingAs($this->vendor1)
            ->put(route('vendor.products.update', ['store' => $this->store1, 'product' => $product2]), $data);

        // Either 403 (unauthorized) or 404 (not found) is acceptable
        $this->assertThat(
            $response->status(),
            $this->logicalOr(
                $this->equalTo(403),
                $this->equalTo(404)
            )
        );
    }

    /** @test */
    public function vendor_cannot_access_other_vendors_categories()
    {
        $category2 = Category::factory()->create(['store_id' => $this->store2->id]);

        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.categories.show', ['store' => $this->store1, 'category' => $category2]));

        $response->assertStatus(404);
    }

    /** @test */
    public function vendor_cannot_access_other_vendors_orders()
    {
        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.orders.index', ['store' => $this->store2]));

        $response->assertStatus(403);
    }

    /** @test */
    public function vendor_cannot_access_other_vendors_settings()
    {
        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.settings.edit', ['store' => $this->store2]));

        $response->assertStatus(403);
    }

    /** @test */
    public function vendor_cannot_update_other_vendors_store_settings()
    {
        $data = [
            'name' => ['en' => 'Hacked Store', 'ar' => 'متجر هاك'],
            'subdomain' => 'hacked',
            'currency_id' => $this->currency->id,
            'is_active' => false,
        ];

        $response = $this->actingAs($this->vendor1)
            ->post(route('vendor.settings.update', ['store' => $this->store2]), $data);

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_vendor_routes()
    {
        $response = $this->get(route('vendor.products.index', ['store' => $this->store1]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_cannot_access_vendor_routes()
    {
        $admin = User::factory()->create();
        Role::create(['name' => 'admin']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->get(route('vendor.products.index', ['store' => $this->store1]));

        $response->assertStatus(403);
    }

    /** @test */
    public function vendor_can_access_own_store_products()
    {
        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.products.index', ['store' => $this->store1]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_access_own_store_settings()
    {
        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.settings.edit', ['store' => $this->store1]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_access_own_store_orders()
    {
        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.orders.index', ['store' => $this->store1]));

        $response->assertStatus(200);
    }
}
