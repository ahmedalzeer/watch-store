<?php

namespace Tests\Feature\Vendor;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $vendor;
    protected $store;
    protected $category;
    protected $brand;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup common data
        $this->vendor = User::factory()->create();
        Role::create(['name' => 'vendor']);
        $this->vendor->assignRole('vendor');

        // Create Currency
        $currency = \App\Models\Currency::create([
            'name' => ['en' => 'US Dollar', 'ar' => 'دولار أمريكي'],
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_active' => true,
        ]);

        $this->store = Store::factory()->create([
            'user_id' => $this->vendor->id,
            'currency_id' => $currency->id,
        ]);

        $this->category = Category::factory()->create(['store_id' => $this->store->id]);
        $this->brand = Brand::factory()->create(['store_id' => $this->store->id]);
    }

    public function test_vendor_can_view_products_index()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.products.index', ['store_id' => $this->store->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Vendor/Products/Index')
            ->has('products')
        );
    }

    public function test_vendor_can_create_product()
    {
        $productData = [
            'store_id' => $this->store->id,
            'name' => ['en' => 'Test Product', 'ar' => 'منتج تجريبي'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'slug' => 'test-product',
            'sku' => 'SKU-TEST-123',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 100,
            'discount_price' => 90,
            'stock' => 50,
            'is_active' => true,
            'main_menu' => true,
            'main_store' => false,
            'condition' => 'new',
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.products.store'), $productData);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-TEST-123',
            'stock' => 50,
            'is_active' => 1,
        ]);
    }

    public function test_vendor_can_update_product()
    {
        $product = Product::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);

        $updateData = [
            'store_id' => $this->store->id,
            'name' => ['en' => 'Updated Product', 'ar' => 'منتج محدث'],
            'slug' => $product->slug, // Keep same slug
            'sku' => $product->sku,   // Keep same SKU
            'category_id' => $this->category->id,
            'price' => 200,
            'stock' => 10,
            'is_active' => false,
            'condition' => 'used',
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.products.update', $product->id), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 10,
            'is_active' => 0,
        ]);
    }

    public function test_vendor_can_delete_product()
    {
        $product = Product::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.products.destroy', ['product' => $product->id, 'store_id' => $this->store->id]));

        $response->assertRedirect();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_vendor_cannot_access_other_store_products()
    {
        $otherUser = User::factory()->create();
        $otherStore = Store::factory()->create(['user_id' => $otherUser->id]);
        
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.products.index', ['store_id' => $otherStore->id]));

        $response->assertStatus(403);
    }
}
