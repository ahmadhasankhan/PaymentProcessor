<?php

namespace App\Services;

use App\Models\CryptoTransaction;
use Illuminate\Support\Facades\Log;
class CryptoPaymentService
{
    protected CryptoSimulationService $cryptoSimulationService;

    public function __construct(CryptoSimulationService $cryptoSimulationService)
    {
        $this->cryptoSimulationService = $cryptoSimulationService;
    }

    public function process($cryptoDetails, int $transactionId)
    {
        $response = $this->cryptoSimulationService->check($cryptoDetails);
        Log::info("Crypto simulation result status: {$response['status']}");
        // Create card transaction record regardless of success/failure
        CryptoTransaction::create([
            'transaction_id' => $transactionId,
            'wallet_address' => $cryptoDetails['wallet_address'],
            'transaction_hash' => $cryptoDetails['transaction_hash'],
            'status' => $response['status']
        ]);
        return $response;
    }
}
