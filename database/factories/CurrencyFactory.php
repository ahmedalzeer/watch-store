<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => [
                'en' => $this->faker->currencyCode(),
                'ar' => $this->faker->currencyCode(),
            ],
            'code' => $this->faker->unique()->currencyCode(),
            'symbol' => $this->faker->randomElement(['$', '£', '€', 'ر.س']),
            'exchange_rate' => 1,
            'is_active' => true,
        ];
    }
}
