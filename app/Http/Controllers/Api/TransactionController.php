<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CryptoPaymentService;
use Illuminate\Http\Request;
use App\Services\CardPaymentService;
use App\Models\Transaction;
use App\Models\Merchant;
use App\Models\CryptoTransaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function store(Request $request, CardPaymentService $cardService, CryptoPaymentService $cryptoService)
    {
        // Validate Input
        $request_data = $request->all();
        Log::info($request_data);
        $validator = Validator::make($request_data, [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'address' => 'required|string',
            'zip_code' => 'required|string|max:10',
            'country' => 'required|string|max:50',
            'amount' => 'required|numeric|min:1',
            'transaction_type' => 'required|in:deposit,withdrawal',
            'merchant' => 'required|in:VISA,MasterCard,USDT,Bitcoin,Litecoin',
            'card_number' => 'nullable|required_if:merchant,VISA,MasterCard|digits:16',
            'cvv' => 'nullable|required_if:merchant,VISA,MasterCard|digits:3',
            'expiry_date' => ['nullable', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/', function ($attribute, $value, $fail) {
                [$month, $year] = explode('/', $value);
                $month = (int) $month;
                $year = (int) ('20' . $year);

                $expiry = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $now = \Carbon\Carbon::now()->startOfMonth();

                if ($expiry->lt($now)) {
                    $fail('The ' . $attribute . ' cannot be in the past.');
                }
            }],
            'wallet_address' => 'nullable|required_if:merchant,USDT,Bitcoin,Litecoin'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Failed', 'error' => $validator->errors()], 400);
        }

        $merchant = Merchant::where('name', $request->merchant)->first();

        if (!$merchant) {
            return response()->json(['status' => 'error', 'message' => 'Invalid merchant.'], 400);
        }

        $transaction = Transaction::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'amount' => $request->amount,
            'transaction_type' => $request->transaction_type,
            'merchant_id' => $merchant->id,
            'status' => 'pending',
        ]);

        if ($merchant->type === 'card') {
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

    public function completeCryptoTransaction(Request $request, int $id)
    {
        $request->validate([
            'transaction_hash' => 'required|string'
        ]);

        $transaction = Transaction::findOrFail($id);

        // Ensure this is a crypto transaction
        $crypto = CryptoTransaction::where('transaction_id', $transaction->id)->first();

        if (!$crypto) {
            return response()->json(['message' => 'It is not a crypto transaction.'], 400);
        }

        if ($crypto->status !== 'pending') {
            return response()->json(['message' => 'Transaction already completed or failed.'], 400);
        }

        $crypto->update(['status' => 'completed']);
        $transaction->update(['status' => 'completed']);

        return response()->json(['message' => 'Transaction marked as completed']);
    }
}


