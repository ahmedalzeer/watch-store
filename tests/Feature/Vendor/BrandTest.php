<?php

namespace Tests\Feature\Vendor;

use App\Models\Brand;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $vendor;
    protected $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = User::factory()->create();
        Role::create(['name' => 'vendor']);
        $this->vendor->assignRole('vendor');

        // Ensure Currency exists for StoreFactory
        \App\Models\Currency::create([
            'name' => ['en' => 'USD', 'ar' => 'USD'],
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_active' => true,
        ]);

        $this->store = Store::factory()->create([
            'user_id' => $this->vendor->id,
        ]);
    }

    public function test_vendor_can_view_brands_index()
    {
        Brand::factory()->count(3)->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.brands.index', ['store_id' => $this->store->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Vendor/Brands/Index')
            ->has('brands.data', 3)
        );
    }

    public function test_vendor_can_create_brand()
    {
        $brandData = [
            'store_id' => $this->store->id,
            'name' => ['en' => 'Test Brand', 'ar' => 'ماركة تجريبية'],
            'slug' => 'test-brand',
            'website' => 'https://brand.com',
            'is_featured' => true,
            'is_active' => true,
            'main_menu' => false,
            'main_store' => false,
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.brands.store'), $brandData);

        $response->assertRedirect();
        $this->assertDatabaseHas('brands', [
            'slug' => 'test-brand',
            'website' => 'https://brand.com',
            'is_featured' => 1,
        ]);
    }

    public function test_vendor_can_update_brand()
    {
        $brand = Brand::factory()->create(['store_id' => $this->store->id]);

        $updateData = [
            'store_id' => $this->store->id,
            'name' => ['en' => 'Updated Brand', 'ar' => 'ماركة محدثة'],
            'slug' => $brand->slug,
            'website' => 'https://updated-brand.com',
            'is_featured' => false,
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.brands.update', $brand->id), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'website' => 'https://updated-brand.com',
            'is_featured' => 0,
        ]);
    }

    public function test_vendor_can_delete_brand()
    {
        $brand = Brand::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.brands.destroy', ['brand' => $brand->id, 'store_id' => $this->store->id]));

        $response->assertRedirect();
        $this->assertSoftDeleted('brands', ['id' => $brand->id]);
    }

    public function test_vendor_cannot_access_other_store_brands()
    {
        $otherUser = User::factory()->create();
        $otherStore = Store::factory()->create(['user_id' => $otherUser->id]);
        
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.brands.index', ['store_id' => $otherStore->id]));

        $response->assertStatus(403);
    }
}
