<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->numberBetween(100, 5000);
        return [
            'store_id' => \App\Models\Store::factory(),
            'category_id' => \App\Models\Category::factory(),
            'brand_id' => \App\Models\Brand::factory(),
            'name' => [
                'en' => $this->faker->words(3, true),
                'ar' => $this->faker->words(3, true),
            ],
            'description' => [
                'en' => $this->faker->paragraph(),
                'ar' => $this->faker->paragraph(),
            ],
            'slug' => $this->faker->slug(),
            'sku' => $this->faker->unique()->bothify('SKU-###-???'),
            'price' => $price,
            'discount_price' => $this->faker->boolean(30) ? $price * 0.8 : null,
            'stock' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
            'main_menu' => $this->faker->boolean(),
            'main_store' => $this->faker->boolean(),
            'condition' => $this->faker->randomElement(['new', 'used', 'refurbished']),
        ];
    }
}
