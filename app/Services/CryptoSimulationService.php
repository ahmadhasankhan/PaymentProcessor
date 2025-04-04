<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class CryptoSimulationService
{
    public function check(array $cryptoDetails): array
    {
        $wallet = $cryptoDetails['wallet_address'];

        // Map of known wallet test addresses to statuses
        $walletStatusMap = [
            // Successful Wallets
            '0xSuccessUSDTAddressERC20' => 'completed',
            'bc1qSuccessBitcoinAddress' => 'completed',
            'ltc1qSuccessLitecoinAddress' => 'completed',

            // Pending Wallets
            'TRPendingUSDTAddressTRC20' => 'pending',
            'bc1qPendingBitcoinAddress' => 'pending',
            'ltc1qPendingLitecoinAddress' => 'pending',

            // Failed Wallets
            '0xFailUSDTAddressERC20' => 'failed',
            'bc1qFailBitcoinAddress' => 'failed',
            'ltc1qFailLitecoinAddress' => 'failed',
        ];

        $status = $walletStatusMap[$wallet] ?? 'failed';

        Log::info("Crypto simulation result for wallet: $wallet => $status");

        $message = match ($status) {
            'completed' => 'Transaction confirmed on blockchain.',
            'pending' => 'Waiting for blockchain confirmation.',
            default => 'Transaction failed due to network or invalid wallet.',
        };

        return [
            'status' => $status,
            'message' => $message
        ];
    }
}
