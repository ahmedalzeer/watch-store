<?php

namespace Tests\Feature\Vendor;

use App\Models\Category;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryTest extends TestCase
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

    public function test_vendor_can_view_categories_index()
    {
        Category::factory()->count(3)->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.categories.index', ['store_id' => $this->store->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Vendor/Categories/Index')
            ->has('categories.data', 3)
        );
    }

    public function test_vendor_can_create_category()
    {
        $categoryData = [
            'store_id' => $this->store->id,
            'name' => ['en' => 'Test Category', 'ar' => 'قسم تجريبي'],
            'slug' => 'test-category',
            'is_active' => true,
            'main_menu' => true,
            'main_store' => false,
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.categories.store'), $categoryData);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'slug' => 'test-category',
            'is_active' => 1,
            'main_menu' => 1,
        ]);
    }

    public function test_vendor_can_update_category()
    {
        $category = Category::factory()->create(['store_id' => $this->store->id]);

        $updateData = [
            'store_id' => $this->store->id,
            'name' => ['en' => 'Updated Category', 'ar' => 'قسم محدث'],
            'slug' => $category->slug,
            'is_active' => false,
            'main_menu' => false,
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.categories.update', $category->id), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => 0,
            'main_menu' => 0,
        ]);
    }

    public function test_vendor_can_delete_category()
    {
        $category = Category::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.categories.destroy', ['category' => $category->id, 'store_id' => $this->store->id]));

        $response->assertRedirect();
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_vendor_cannot_access_other_store_categories()
    {
        $otherUser = User::factory()->create();
        $otherStore = Store::factory()->create(['user_id' => $otherUser->id]);
        
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.categories.index', ['store_id' => $otherStore->id]));

        $response->assertStatus(403);
    }
}
