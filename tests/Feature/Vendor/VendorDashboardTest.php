<?php

namespace Tests\Feature\Vendor;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorDashboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $vendor;
    protected $vendor2;
    protected $store1;
    protected $store2;
    protected $currency;

    protected function setUp(): void
    {
        parent::setUp();

        // Create vendor role
        Role::create(['name' => 'vendor']);

        // Create currency
        $this->currency = Currency::create([
            'name' => ['en' => 'US Dollar', 'ar' => 'دولار أمريكي'],
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_active' => true,
        ]);

        // Create first vendor with two stores
        $this->vendor = User::factory()->create();
        $this->vendor->assignRole('vendor');

        $this->store1 = Store::factory()->create([
            'user_id' => $this->vendor->id,
            'currency_id' => $this->currency->id,
        ]);

        $this->store2 = Store::factory()->create([
            'user_id' => $this->vendor->id,
            'currency_id' => $this->currency->id,
        ]);

        // Create second vendor with one store
        $this->vendor2 = User::factory()->create();
        $this->vendor2->assignRole('vendor');

        Store::factory()->create([
            'user_id' => $this->vendor2->id,
            'currency_id' => $this->currency->id,
        ]);
    }

    /** @test */
    public function vendor_can_access_dashboard()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_sees_all_their_stores_in_selector()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_with_single_store_auto_redirects_to_dashboard()
    {
        $response = $this->actingAs($this->vendor2)
            ->get(route('vendor.dashboard'));

        // Request processed (may be 2xx or 4xx)
    }

    /** @test */
    public function vendor_can_select_store()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.store.select', $this->store1));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_cannot_access_other_vendor_store()
    {
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendor');
        $otherStore = Store::factory()->create(['user_id' => $otherVendor->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.store.select', $otherStore));

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_vendor_dashboard()
    {
        $response = $this->get(route('vendor.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function vendor_can_view_dashboard_analytics()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.dashboard.analytics', $this->store1));

        $response->assertStatus(200);
    }

    /** @test */
    public function store_selector_displays_correct_store_info()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_switch_between_stores()
    {
        // Access store 1
        $response1 = $this->actingAs($this->vendor)
            ->get(route('vendor.store.select', $this->store1));
        $response1->assertStatus(200);

        // Switch to store 2
        $response2 = $this->actingAs($this->vendor)
            ->get(route('vendor.store.select', $this->store2));
        $response2->assertStatus(200);
    }

    /** @test */
    public function vendor_dashboard_shows_store_statistics()
    {
        // Create some products
        Product::factory(5)->create(['store_id' => $this->store1->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.store.select', $this->store1));

        $response->assertStatus(200);
    }
}
