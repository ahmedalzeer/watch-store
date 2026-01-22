<?php

namespace Tests\Feature\Vendor;

use App\Models\Banner;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BannerTest extends TestCase
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

    public function test_vendor_can_view_banners_index()
    {
        Banner::factory()->count(3)->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.banners.index', ['store_id' => $this->store->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Vendor/Banners/Index')
            ->has('banners.data', 3)
        );
    }

    public function test_vendor_can_create_banner()
    {
        $bannerData = [
            'store_id' => $this->store->id,
            'title' => ['en' => 'Test Banner', 'ar' => 'بانر تجريبي'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'type' => 'main',
            'link' => 'https://example.com',
            'active' => true,
            'order' => 1,
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.banners.store'), $bannerData);

        $response->assertRedirect();
        $this->assertDatabaseHas('banners', [
            'type' => 'main',
            'link' => 'https://example.com',
            'active' => 1,
        ]);
    }

    public function test_vendor_can_update_banner()
    {
        $banner = Banner::factory()->create(['store_id' => $this->store->id]);

        $updateData = [
            'store_id' => $this->store->id,
            'title' => ['en' => 'Updated Banner', 'ar' => 'بانر محدث'],
            'description' => ['en' => 'Updated Desc', 'ar' => 'وصف محدث'],
            'type' => 'footer',
            'link' => 'https://updated.com',
            'active' => false,
            'order' => 2,
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.banners.update', $banner->id), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'type' => 'footer',
            'link' => 'https://updated.com',
            'active' => 0,
        ]);
    }

    public function test_vendor_can_delete_banner()
    {
        $banner = Banner::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.banners.destroy', ['banner' => $banner->id, 'store_id' => $this->store->id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }

    public function test_vendor_cannot_access_other_store_banners()
    {
        $otherUser = User::factory()->create();
        $otherStore = Store::factory()->create(['user_id' => $otherUser->id]);
        
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.banners.index', ['store_id' => $otherStore->id]));

        $response->assertStatus(403);
    }
}
