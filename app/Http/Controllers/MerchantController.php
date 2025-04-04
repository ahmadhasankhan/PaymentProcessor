<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function processCard(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|size:16',
            'cvv' => 'required|string|size:3',
            'expiry_date' => 'required|string|regex:/^(0[1-9]|1[0-2])\/([0-9]{2})$/',
        ]);

        if ($request->card_number === '4000000000000002') {
            return response()->json(['status' => 'Failed', 'message' => 'Card declined'], 400);
        }

        return response()->json(['status' => 'Success', 'message' => 'Payment successful']);
    }

    public function processCrypto(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        $pendingWallets = ['TRPendingUSDTAddressTRC20', 'bc1qPendingBitcoinAddress', 'ltc1qPendingLitecoinAddress'];
        $failedWallets = ['0xFailUSDTAddressERC20', 'bc1qFailBitcoinAddress', 'ltc1qFailLitecoinAddress'];

        if (in_array($request->wallet_address, $failedWallets)) {
            return response()->json(['status' => 'Failed', 'message' => 'Invalid crypto wallet address'], 400);
        } elseif (in_array($request->wallet_address, $pendingWallets)) {
            return response()->json(['status' => 'Pending', 'message' => 'Transaction is pending']);
        }

        return response()->json(['status' => 'Success', 'message' => 'Transaction successful']);
    }
}
