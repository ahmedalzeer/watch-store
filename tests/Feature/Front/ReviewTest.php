<?php

namespace Tests\Feature\Front;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'customer']);
    }

    public function test_customer_can_submit_review()
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(route('review.store'), [
            'product_id' => $product->id,
            'rating' => 5,
            'review' => 'Excellent watch!'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'review' => 'Excellent watch!'
        ]);
    }

    public function test_prevent_duplicate_reviews()
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $product = Product::factory()->create();

        ProductReview::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($user)->post(route('review.store'), [
            'product_id' => $product->id,
            'rating' => 4,
            'review' => 'Another review'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertCount(1, ProductReview::where('user_id', $user->id)->where('product_id', $product->id)->get());
    }
}
