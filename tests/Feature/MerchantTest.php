<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_card_payment_success()
    {
        $response = $this->postJson('/api/merchant/card', [
            'card_number' => '4111111111111111',
            'expiry_date' => '12/29',
            'cvv' => '123',
            "amount" => 50
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_process_crypto_payment_success()
    {
        $response = $this->postJson('/api/merchant/crypto', [
            'wallet_address' => 'bc1qSuccessBitcoinAddress',
            "amount" => 10
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'completed']);
    }

    public function test_process_crypto_payment_failure()
    {
        $response = $this->postJson('/api/merchant/crypto', [
            'wallet_address' => '',
            'transaction_hash' => ''
        ]);

        $response->assertStatus(422); // If validation applied
    }
}
