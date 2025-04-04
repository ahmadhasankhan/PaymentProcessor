<?php

namespace Database\Factories;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MerchantFactory extends Factory
{
    protected $model = Merchant::class;

    public function definition()
    {
        $type = fake()->randomElement(['card', 'crypto']);

        return [
            'name' => $type === 'card' ? 'VISA' : 'Bitcoin',
            'type' => $type,
        ];
    }
}
