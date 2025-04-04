<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Merchant>
 */
class MerchantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['card', 'crypto']);
        return [
            'name' => $type === 'card'
                ? $this->faker->randomElement(['VISA', 'MasterCard'])
                : $this->faker->randomElement(['USDT', 'Bitcoin', 'Litecoin']),
            'type' => $type,
        ];
    }
}
