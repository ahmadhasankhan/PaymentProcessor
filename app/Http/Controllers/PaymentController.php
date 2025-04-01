<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentService;
use App\Models\Merchant;

class PaymentController extends Controller {
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function showPaymentForm()
        {
            $merchants = Merchant::all();
            return view('payment-form', compact('merchants'));
        }

    public function processPayment(Request $request) {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'zip_code' => 'required',
            'country' => 'required',
            'amount' => 'required|numeric',
            'transaction_type' => 'required',
            'merchant_id' => 'required|exists:merchants,id',
        ]);

        $transactionData = $request->all();
        Log::info('Received payment request with transaction data:', $transactionData);
        $transaction = $this->paymentService->processPayment($transactionData);

        if ($transaction->status === 'Completed') {
            return response()->json([
                'message' => 'Payment successful',
                'transaction' => $transaction
            ], 200);
        } elseif ($transaction->status === 'Pending') {
                return response()->json([
                    'message' => 'Payment Pending, please check the payment status after sometime',
                    'transaction' => $transaction
                ], 200);
        } else {
            return response()->json([
                'message' => 'Payment failed',
                'error' => $transaction->error_message ?? 'Unknown error occurred'
            ], 400);
        }
    }

}
