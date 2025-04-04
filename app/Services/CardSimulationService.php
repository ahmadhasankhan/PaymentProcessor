<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;

class CardSimulationService
{
    public function check(array $cardDetails): array
    {
        // Basic fraud check
        if ($this->isFraudulentCard($cardDetails['card_number'])) {
            Log::error('Transaction declined due to fraud.');
            return [
                'status' => 'failed',
                'message' => 'Transaction declined due to fraud.'
            ];
        }

        // Success card check
        if ($this->isSuccessCard($cardDetails['card_number'])) {
            Log::info('Transaction approved.');
            return [
                'status' => 'success',
                'message' => 'Transaction approved.'
            ];
        }

        Log::error('Default Invalid Card.');
        return [
            'status' => 'failed',
            'message' => 'Invalid card details.'
        ];
    }

    private function isFraudulentCard($cardNumber)
    {
        $fraudCards = ['1000000000000000', '2000000000000000', '3000000000000000', '4000000000000000'];
        return in_array($cardNumber, $fraudCards);
    }

    private function isSuccessCard($cardNumber)
    {
        $fraudCards = ['4111111111111111', '4242424242424242'];
        return in_array($cardNumber, $fraudCards);
    }
}
