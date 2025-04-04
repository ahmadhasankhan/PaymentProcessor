<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => 'Test Address',
            'zip_code' => '12345',
            'country' => 'India',
            'amount' => 100.00,
            'status' => 'pending',
            'merchant_id' => \App\Models\Merchant::factory(),
        ];
    }
}
