<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => \App\Models\Store::factory(),
            'name' => [
                'en' => $this->faker->company(),
                'ar' => $this->faker->company(),
            ],
            'slug' => $this->faker->slug(),
            'is_active' => true,
        ];
    }
}
