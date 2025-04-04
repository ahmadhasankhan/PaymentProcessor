<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\CardTransaction;

class CardPaymentService
{
    protected CardSimulationService $cardSimulationService;

    public function __construct(CardSimulationService $cardSimulationService)
    {
        $this->cardSimulationService = $cardSimulationService;
    }
    public function process(array $cardDetails, int $transactionId): array
    {
        // Mask or sanitize card number (store only last 4 digits) or we can also encrypt it if required
        $maskedCardNumber = substr($cardDetails['card_number'], -4);

        // Create card transaction record regardless of success/failure
        $card_transaction = CardTransaction::create([
            'transaction_id' => $transactionId,
            'card_number' => $maskedCardNumber,
            'expiry_date' => $cardDetails['expiry_date'],
            'cvv' => $cardDetails['cvv'],
        ]);
        Log::info("Card Transaction was created with id: {$card_transaction}");
        return $this->cardSimulationService->check($cardDetails);
    }
}
