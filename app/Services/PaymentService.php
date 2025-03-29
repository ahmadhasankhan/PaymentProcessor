<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Charge;
use App\Models\Transaction;
use App\Models\Merchant;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentService
{
    public function processPayment($data)
    {
        Log::info('Received payment request:', $data);
        $merchant = Merchant::find($data['merchant_id']);

        if (!$merchant) {
            return (object)[
                'status' => 'Failed',
                'error_message' => 'Invalid merchant selected.'
            ];
        }

        if ($merchant->type === 'card') {
            if (!isset($data['stripeToken'])) {
                return (object)[
                    'status' => 'Failed',
                    'error_message' => 'Stripe token is missing.'
                ];
            }
            return $this->processCardPayment($data, $merchant);
        } else {
            return $this->processCryptoPayment($data, $merchant);
        }
    }

    private function processCardPayment($data, $merchant)
    {
        $transaction = Transaction::create([
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'address'          => $data['address'],
            'zip_code'         => $data['zip_code'],
            'country'          => $data['country'],
            'amount'           => $data['amount'],
            'transaction_type' => $data['transaction_type'],
            'merchant_id'      => $merchant->id,
            'status'           => 'Pending'
        ]);

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $charge = Charge::create([
                'amount'        => $data['amount'] * 100, // Convert to cents
                'currency'      => 'usd', // TODO: Remove hard coded value
                'source'        => $data['stripeToken'],
                'description'   => "Payment from {$data['first_name']} {$data['last_name']}",
            ]);

            $transaction->update([
                'transaction_id' => $charge->id,
                'status'         => 'Completed',
            ]);

            return $transaction;
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $statusCode = $e->getHttpStatus();
            $stripeHeaders = $e->getHttpHeaders();
            $stripeRequestId = $stripeHeaders['request-id'] ?? 'Unknown Request ID';
            Log::error("Stripe Payment Failed: $errorMessage (Status $statusCode) (Request $stripeRequestId)");

            $transaction->update([
                'status'         => 'Failed',
                'transaction_id' => $stripeRequestId,
                'error_message'  => $errorMessage
            ]);

            return $transaction;
        }
    }

    private function processCryptoPayment($data, $merchant)
    {
        try {
            // Simulate crypto transaction success or failure
            $successWallets = [
                '0xSuccessUSDTAddressERC20',
                'bc1qSuccessBitcoinAddress',
                'ltc1qSuccessLitecoinAddress'
            ];

            $transactionStatus = in_array($data['wallet_address'], $successWallets) ? 'Completed' : 'Pending';


            $transaction = Transaction::create([
                'first_name'       => $data['first_name'],
                'last_name'        => $data['last_name'],
                'address'          => $data['address'],
                'zip_code'         => $data['zip_code'],
                'country'          => $data['country'],
                'amount'           => $data['amount'],
                'transaction_type' => $data['transaction_type'],
                'transaction_id'   => $data['transaction_hash'],
                'wallet_address'   => $data['wallet_address'],
                'merchant_id'      => $merchant->id,
                'status'           => $transactionStatus,
            ]);

            Log::info("Crypto payment initiated for transaction ID: {$transaction->transaction_id}");

            return $transaction;
        } catch (Exception $e) {
            Log::error('Crypto Payment Failed: ' . $e->getMessage());

            return Transaction::create([
                'first_name'      => $data['first_name'],
                'last_name'       => $data['last_name'],
                'amount'          => $data['amount'],
                'merchant_id'     => $merchant->id,
                'status'          => 'Failed',
                'error_message'   => $e->getMessage(),
            ]);
        }
    }
}
