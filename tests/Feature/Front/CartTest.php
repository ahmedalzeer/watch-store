<?php

namespace Tests\Feature\Front;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_simple_product_to_cart()
    {
        $product = Product::factory()->create(['price' => 1000]);

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('cart_count', 2);
        
        $cart = Session::get('cart');
        $this->assertCount(1, $cart);
        $key = "cart_{$product->id}";
        $this->assertEquals(2, $cart[$key]['quantity']);
        $this->assertEquals(1000, $cart[$key]['price']);
    }

    public function test_can_add_variant_product_to_cart()
    {
        $product = Product::factory()->create();
        
        $colorAttr = Attribute::factory()->create(['name' => ['en' => 'Color']]);
        $redValue = AttributeValue::factory()->create(['attribute_id' => $colorAttr->id, 'value' => ['en' => 'Red']]);
        
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price' => 1500,
            'sku' => 'RED-WATCH'
        ]);
        $variant->attributeValues()->attach($redValue->id);

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'attribute_value_ids' => [$redValue->id]
        ]);

        $response->assertStatus(200);
        
        $cart = Session::get('cart');
        $key = "cart_{$product->id}_{$variant->id}";
        $this->assertArrayHasKey($key, $cart);
        $this->assertEquals(1500, $cart[$key]['price']);
        $this->assertEquals('Red', $cart[$key]['attributes']['Color']);
    }

    public function test_can_update_cart_quantity()
    {
        $product = Product::factory()->create();
        $key = "cart_{$product->id}";
        
        Session::put('cart', [
            $key => [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 100,
                'quantity' => 1,
            ]
        ]);

        $response = $this->post(route('cart.update'), [
            'cart' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5
                ]
            ]
        ]);

        $response->assertStatus(200);
        $this->assertEquals(5, Session::get('cart')[$key]['quantity']);
    }

    public function test_can_remove_from_cart()
    {
        $product = Product::factory()->create();
        $key = "cart_{$product->id}";
        
        Session::put('cart', [
            $key => [
                'product_id' => $product->id,
                'quantity' => 1,
            ]
        ]);

        $response = $this->post(route('cart.remove'), [
            'product_id' => $product->id
        ]);

        $response->assertStatus(200);
        $this->assertEmpty(Session::get('cart'));
    }
}
