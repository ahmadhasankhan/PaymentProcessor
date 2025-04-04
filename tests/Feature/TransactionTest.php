<?php

namespace Tests\Feature;

use App\Models\CryptoTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Merchant;
use App\Models\Transaction;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_transaction()
    {
        $merchant = Merchant::factory()->create([
            'name' => 'VISA',
            'type' => 'card'
        ]);

        $response = $this->postJson('/api/transaction', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '123 Street',
            'zip_code' => '12345',
            'country' => 'India',
            'amount' => 500,
            'transaction_type' => 'deposit',
            'merchant' => $merchant->name,
            'card_number' => '4111111111111111',
            'expiry_date' => '12/29',
            'cvv' => '123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', [
            'first_name' => 'John',
            'amount' => 500,
        ]);
    }

    public function test_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/transaction', [
            'amount' => 500
        ]);

        $response->assertStatus(400);
    }

    public function test_can_complete_crypto_transaction()
    {
        $cryptoTransaction = CryptoTransaction::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/transaction/{$cryptoTransaction->transaction->id}/complete", [
            'transaction_hash' => '0x89868fdc7ec8e3a9533bb7916fd1aa5683f34c0e0eb1bab1795e8083b022acb3'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', [
            'id' => $cryptoTransaction->transaction->id,
            'status' => 'completed',
        ]);
    }
}
