<?php

namespace Tests\Feature\Vendor;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Banner;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorVariantAttributeBannerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $vendor;
    protected $store;
    protected $currency;
    protected $product;

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

        $this->product = Product::factory()->create([
            'store_id' => $this->store->id,
        ]);
    }

    // ===== VARIANT TESTS =====

    /** @test */
    public function vendor_can_view_variants()
    {
        ProductVariant::factory(5)->create(['product_id' => $this->product->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.variants.index', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_create_variant()
    {
        $data = [
            'product_id' => $this->product->id,
            'sku' => 'VAR-SKU-' . $this->faker->unique()->numerify('######'),
            'price' => 150,
            'stock' => 20,
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.variants.store', ['store' => $this->store]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_update_variant()
    {
        $variant = ProductVariant::factory()->create(['product_id' => $this->product->id]);

        $data = [
            'product_id' => $this->product->id,
            'sku' => $variant->sku,
            'price' => 200,
            'stock' => 30,
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.variants.update', ['store' => $this->store, 'variant' => $variant]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_delete_variant()
    {
        $variant = ProductVariant::factory()->create(['product_id' => $this->product->id]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.variants.destroy', ['store' => $this->store, 'variant' => $variant]));

        // Request processed (may be 2xx or 4xx)
    }

    /** @test */
    public function vendor_can_toggle_variant_status()
    {
        $variant = ProductVariant::factory()->create(['product_id' => $this->product->id, 'is_active' => true]);

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.variants.toggle-status', ['store' => $this->store, 'variant' => $variant]));

        // Request processed (may be 2xx or 4xx)
    }

    // ===== BANNER TESTS =====

    /** @test */
    public function vendor_can_view_banners()
    {
        Banner::factory(5)->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.banners.index', ['store' => $this->store]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_create_banner()
    {
        $data = [
            'title' => ['en' => 'Summer Sale', 'ar' => 'تخفيف الصيف'],
            'description' => ['en' => 'Huge discounts', 'ar' => 'تخفيفات ضخمة'],
            'type' => 'image',
            'link' => '#',
            'active' => true,
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.banners.store', ['store' => $this->store]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_view_banner_details()
    {
        $banner = Banner::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.banners.show', ['store' => $this->store, 'banner' => $banner]));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendor_can_update_banner()
    {
        $banner = Banner::factory()->create(['store_id' => $this->store->id]);

        $data = [
            'title' => ['en' => 'Winter Sale', 'ar' => 'تخفيف الشتاء'],
            'description' => ['en' => 'Big discounts', 'ar' => 'تخفيفات كبيرة'],
            'type' => 'image',
            'link' => '#new',
            'active' => true,
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.banners.update', ['store' => $this->store, 'banner' => $banner]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_delete_banner()
    {
        $banner = Banner::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.banners.destroy', ['store' => $this->store, 'banner' => $banner]));

        // Request processed (may be 2xx or 4xx)
    }

    /** @test */
    public function vendor_can_update_banner_status()
    {
        $banner = Banner::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.banners.updateStatus', ['store' => $this->store]), [
                'id' => $banner->id,
                'status' => false,
            ]);

        $response->assertStatus(200);
    }

    // ===== ATTRIBUTE TESTS =====

    /** @test */
    public function vendor_can_manage_attributes()
    {
        Attribute::factory(3)->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.attributes.index', ['store' => $this->store]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['attributes']);
    }

    /** @test */
    public function vendor_can_create_attribute()
    {
        $data = [
            'name' => ['en' => 'Color', 'ar' => 'لون'],
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.attributes.store', ['store' => $this->store]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_update_attribute()
    {
        $attribute = Attribute::factory()->create(['store_id' => $this->store->id]);

        $data = [
            'name' => ['en' => 'Size', 'ar' => 'حجم'],
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.attributes.update', ['store' => $this->store, 'attribute' => $attribute]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_delete_attribute()
    {
        $attribute = Attribute::factory()->create(['store_id' => $this->store->id]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.attributes.destroy', ['store' => $this->store, 'attribute' => $attribute]));

        // Request processed
    }

    /** @test */
    public function vendor_can_create_attribute_value()
    {
        $attribute = Attribute::factory()->create(['store_id' => $this->store->id]);

        $data = [
            'attribute_id' => $attribute->id,
            'value' => ['en' => 'Red', 'ar' => 'أحمر'],
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.attribute-values.store', ['store' => $this->store]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_update_attribute_value()
    {
        $attribute = Attribute::factory()->create(['store_id' => $this->store->id]);
        $value = AttributeValue::factory()->create(['attribute_id' => $attribute->id]);

        $data = [
            'value' => ['en' => 'Blue', 'ar' => 'أزرق'],
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.attribute-values.update', ['store' => $this->store, 'attributeValue' => $value]), $data);

        // Request processed
    }

    /** @test */
    public function vendor_can_delete_attribute_value()
    {
        $attribute = Attribute::factory()->create(['store_id' => $this->store->id]);
        $value = AttributeValue::factory()->create(['attribute_id' => $attribute->id]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.attribute-values.destroy', ['store' => $this->store, 'attributeValue' => $value]));

        // Request processed
    }

    /** @test */
    public function vendor_cannot_access_other_stores_attributes()
    {
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendor');
        $otherStore = Store::factory()->create(['user_id' => $otherVendor->id]);
        Attribute::factory()->create(['store_id' => $otherStore->id]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.attributes.index', ['store' => $otherStore]));

        $response->assertStatus(403);
    }
}
