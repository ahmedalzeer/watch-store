<?php

namespace Tests\Feature\Front;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'customer']);
    }

    public function test_guest_cannot_toggle_wishlist()
    {
        $product = Product::factory()->create();
        $response = $this->post(route('customer.wishlist.toggle'), ['product_id' => $product->id]);
        $response->assertRedirect(route('login'));
    }

    public function test_customer_can_toggle_wishlist()
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(route('customer.wishlist.toggle'), [
            'product_id' => $product->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'added');
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        // Toggle again to remove
        $response = $this->actingAs($user)->post(route('customer.wishlist.toggle'), [
            'product_id' => $product->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'removed');
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    public function test_customer_can_view_wishlist()
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $product = Product::factory()->create();
        
        $user->wishlistProducts()->attach($product->id);

        $response = $this->actingAs($user)->get(route('customer.wishlist.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('FrontEnd/Wishlist')
            ->has('products', 1)
        );
    }
}
