<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
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
            'user_id' => \App\Models\User::factory(),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['stripe', 'cod']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'total' => $this->faker->numberBetween(100, 10000),
            'shipping_details' => [
                'name' => $this->faker->name(),
                'email' => $this->faker->email(),
                'phone' => $this->faker->phoneNumber(),
                'address' => $this->faker->address(),
                'city' => $this->faker->city(),
                'country' => $this->faker->country(),
                'postal_code' => $this->faker->postcode(),
            ],
        ];
    }
}
