<?php

namespace Database\Factories;

use App\Models\CryptoTransaction;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class CryptoTransactionFactory extends Factory
{
    protected $model = CryptoTransaction::class;

    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'wallet_address' => $this->faker->regexify('0x[a-f0-9]{40}'),
            'transaction_hash' => $this->faker->sha256,
            'status' => 'pending',
        ];
    }
}
