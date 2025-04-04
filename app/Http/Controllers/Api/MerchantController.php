<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CardSimulationService;
use App\Services\CryptoSimulationService;

class MerchantController extends Controller
{
    protected CardSimulationService $cardSimulationService;
    protected CryptoSimulationService $cryptoSimulationService;

    public function __construct(
        CardSimulationService $cardSimulationService,
        CryptoSimulationService $cryptoSimulationService
    ) {
        $this->cardSimulationService = $cardSimulationService;
        $this->cryptoSimulationService = $cryptoSimulationService;
    }

    public function processCardPayment(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'card_number' => 'required|string|size:16',
            'cvv' => 'required|string|size:3',
            'expiry_date' => [
                'required',
                'string',
                'regex:/^(0[1-9]|1[0-2])\/([0-9]{2})$/' // Ensure correct regex format
            ],
            'amount' => 'required|numeric|min:1',
        ]);

        return $this->cardSimulationService->check($validated);
    }

    public function processCryptoPayment(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'wallet_address' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $response = $this->cryptoSimulationService->check($validated);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message']
        ], $response['status'] === 'Success' ? 200 : ($response['status'] === 'Pending' ? 202 : 400));

    }
}
