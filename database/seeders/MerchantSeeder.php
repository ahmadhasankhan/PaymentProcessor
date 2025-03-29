<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchants = [
            ['name' => 'VISA', 'type' => 'card'],
            ['name' => 'MasterCard', 'type' => 'card'],
            ['name' => 'USDT', 'type' => 'crypto'],
            ['name' => 'Bitcoin', 'type' => 'crypto'],
            ['name' => 'Litecoin', 'type' => 'crypto'],
        ];

        DB::table('merchants')->insert($merchants);
    }
}