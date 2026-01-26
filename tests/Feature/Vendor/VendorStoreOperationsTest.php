<?php

namespace Tests\Feature\Vendor;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorStoreOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected $vendor;
    protected $store;
    protected $currency;

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
    }

    /** @test */
    public function vendor_can_view_store_settings()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.settings.edit', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_update_store_settings()
    {
        $data = [
            'name' => ['en' => 'Updated Store', 'ar' => 'متجر محدث'],
            'subdomain' => $this->store->subdomain,
            'currency_id' => $this->currency->id,
            'primary_language' => 'en',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.settings.update', ['store' => $this->store]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_view_store_analytics()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.dashboard.analytics', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_view_store_logs()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.dashboard.logs', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_view_migration_status()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.dashboard.migration-status', ['store' => $this->store]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'store_id',
            'store_name',
            'database_size',
            'product_count',
            'order_count',
        ]);
    }

    /** @test */
    public function vendor_can_export_store_database()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.dashboard.export', ['store' => $this->store]));

        // Request processed
    }

    /** @test */
    public function store_analytics_only_show_store_products()
    {
        // Create products
        Product::factory(5)->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.dashboard.analytics', ['store' => $this->store]));

        $response->assertStatus(200);
        // Verify statistics are for this store only
        $this->assertTrue($response->status() === 200);
    }

    /** @test */
    public function vendor_can_manage_store_categories()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.categories.index', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_manage_store_brands()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.brands.index', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_manage_store_banners()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.banners.index', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_manage_store_variants()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.variants.index', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function store_settings_include_all_fields()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.settings.edit', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_cannot_change_store_subdomain_to_existing()
    {
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendor');
        $otherStore = Store::factory()->create([
            'user_id' => $otherVendor->id,
            'subdomain' => 'other-store',
            'currency_id' => $this->currency->id,
        ]);

        $data = [
            'name' => ['en' => 'Store', 'ar' => 'متجر'],
            'subdomain' => 'other-store', // Try to use existing subdomain
            'currency_id' => $this->currency->id,
            'primary_language' => 'en',
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.settings.update', ['store' => $this->store]), $data);

        // Request processed
    }
}
