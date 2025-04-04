<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Merchant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MerchantTest extends TestCase
{
    use RefreshDatabase;

    public function test_merchant_can_be_created()
    {
        $merchant = Merchant::factory()->create();

        $this->assertDatabaseHas('merchants', [
            'id' => $merchant->id,
        ]);
    }
}
