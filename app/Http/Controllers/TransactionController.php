<?php

namespace App\Http\Controllers;

use App\Services\CardPaymentService;
use App\Services\CryptoPaymentService;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Merchant;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function showPaymentForm()
    {
        $merchants = Merchant::all();
        return view('payment-form', compact('merchants'));
    }

    public function process(Request $request, CardPaymentService $cardService, CryptoPaymentService $cryptoService)
    {
        $request_data = $request->all();
        Log::info('Received payment request with transaction data:', $request_data);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string',
            'zip_code' => 'required|string',
            'country' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'transaction_type' => 'required|in:Deposit,Withdrawal',
            'merchant' => 'required|exists:merchants,name',
        ]);

        $merchant = Merchant::where('name', $request->merchant)->firstOrFail();
        $transaction = Transaction::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'amount' => $request->amount,
            'transaction_type' => $request->transaction_type,
            'merchant_id' => $merchant->id,
            'status' => 'initiated',
        ]);

        if ($merchant->type === 'card') {
            Log::info('Calling cardService');
            $cardResponse = $cardService->process(['card_number' => $request->card_number, 'cvv' => $request->cvv,
                'expiry_date' => $request->expiry_date, 'amount' => $request->amount], $transaction->id);

            $statusAction = match ($cardResponse['status']) {
                'failed' => function () use ($transaction, $cardResponse) {
                    $transaction->status = 'failed';
                    $transaction->error_message = $cardResponse['message'];
                    $transaction->save();
                    return response()->json(['message' => 'Transaction failed', 'error' => $cardResponse['message']], 400);
                },
                'success' => function () use ($transaction) {
                    $transaction->status = 'success';
                    $transaction->save();
                    return response()->json(['message' => 'Transaction processed successfully.', 'transaction' => $transaction], 200);
                },
                default => fn() => null,
            };
            if ($response = $statusAction()) {
                Log::info("Card has been processed {$response}");
                return $response;
            }
        } else {
            $cryptoResponse = $cryptoService->process($request_data, $transaction->id);
            $statusAction = match ($cryptoResponse['status']) {
                'failed' => function () use ($transaction, $cryptoResponse) {
                    $transaction->status = 'failed';
                    $transaction->error_message = $cryptoResponse['message'];
                    $transaction->save();
                    return response()->json([
                        'message' => "Transaction failed, please note the transaction_id: {$transaction->id} for future reference",
                        'error' => $cryptoResponse['message']
                    ], 400);
                },
                'pending' => function () use ($transaction) {
                    $transaction->status = 'pending';
                    $transaction->save();
                    return response()->json([
                        'message' => 'Transaction pending blockchain confirmation',
                        'transaction' => $transaction
                    ], 202);
                },
                'completed' => function () use ($transaction) {
                    $transaction->status = 'success';
                    $transaction->save();
                    return response()->json([
                        'message' => 'Transaction completed successfully',
                        'transaction' => $transaction
                    ]);
                },
                default => fn () => null,
            };

            if ($response = $statusAction()) {
                return $response;
            }
        }

        Log::info("Transaction processed: ", $transaction->toArray());
        return response()->json([
            'message' => 'Something went wrong.',
            'transaction' => $transaction
        ], 201);
    }
}
