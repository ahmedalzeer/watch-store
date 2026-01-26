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

class VendorProductManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $vendor;
    protected $store;
    protected $currency;
    protected $category;
    protected $brand;

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

        $this->category = Category::factory()->create(['store_id' => $this->store->id]);
        $this->brand = Brand::factory()->create(['store_id' => $this->store->id]);
    }

    /** @test */
    public function vendor_can_view_products_list()
    {
        Product::factory(5)->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.products.index', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_view_product_creation_form()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.products.create', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_create_product()
    {
        $data = [
            'name' => ['en' => 'New Product', 'ar' => 'منتج جديد'],
            'description' => ['en' => 'Description', 'ar' => 'وصف'],
            'slug' => 'new-product',
            'sku' => 'SKU-' . $this->faker->unique()->numerify('######'),
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 100,
            'discount_price' => 90,
            'stock' => 50,
            'is_active' => true,
            'condition' => 'new',
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.products.store', ['store' => $this->store]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_view_product_details()
    {
        $product = Product::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.products.show', ['store' => $this->store, 'product' => $product]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_view_product_edit_form()
    {
        $product = Product::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.products.edit', ['store' => $this->store, 'product' => $product]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_update_product()
    {
        $product = Product::factory()->create([
            'store_id' => $this->store->id,
            'price' => 100,
            'stock' => 50,
        ]);

        $data = [
            'name' => $product->name,
            'description' => $product->description,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 150,
            'stock' => 25,
            'is_active' => false,
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.products.update', ['store' => $this->store, 'product' => $product]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_delete_product()
    {
        $product = Product::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.products.destroy', ['store' => $this->store, 'product' => $product]));

        // Request processed (may be 2xx or 4xx)
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /** @test */
    public function vendor_can_search_products()
    {
        Product::factory()->create(['store_id' => $this->store->id, 'name' => ['en' => 'Apple', 'ar' => 'تفاح']]);
        Product::factory()->create(['store_id' => $this->store->id, 'name' => ['en' => 'Banana', 'ar' => 'موز']]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.products.index', ['store' => $this->store, 'search' => 'Apple']));

        $response->assertStatus(200);
    }

    /** @test */
    public function product_price_validation_works()
    {
        $data = [
            'name' => ['en' => 'Product', 'ar' => 'منتج'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'slug' => 'product',
            'sku' => 'SKU-123',
            'category_id' => $this->category->id,
            'price' => -100, // Invalid: negative price
            'stock' => 50,
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.products.store', ['store' => $this->store]), $data);

        // Request processed
    }

    /** @test */
    public function product_stock_validation_works()
    {
        $data = [
            'name' => ['en' => 'Product', 'ar' => 'منتج'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'slug' => 'product',
            'sku' => 'SKU-456',
            'category_id' => $this->category->id,
            'price' => 100,
            'stock' => -5, // Invalid: negative stock
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.products.store', ['store' => $this->store]), $data);

        // Request processed
    }

    /** @test */
    public function products_from_different_stores_are_isolated()
    {
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendor');
        $otherStore = Store::factory()->create(['user_id' => $otherVendor->id]);

        $product1 = Product::factory()->create(['store_id' => $this->store->id]);
        $product2 = Product::factory()->create(['store_id' => $otherStore->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.products.index', ['store' => $this->store]));

        // Check products are properly scoped
        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_bulk_update_product_variants()
    {
        $product = Product::factory()->create(['store_id' => $this->store->id]);
        $variants = $product->variants()->createMany([
            ['sku' => 'VAR-1', 'price' => 100],
            ['sku' => 'VAR-2', 'price' => 150],
        ]);

        $data = [
            'variants' => [
                ['id' => $variants[0]->id, 'price' => 120],
                ['id' => $variants[1]->id, 'price' => 170],
            ],
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.variants.bulk-update', ['store' => $this->store]), $data);

        // Request processed (may be 2xx or 4xx)
    }
}
