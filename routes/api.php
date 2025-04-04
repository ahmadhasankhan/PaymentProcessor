<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\MerchantController;

Route::post('/transaction', [TransactionController::class, 'store']);
Route::post('/transaction/{id}/complete', [TransactionController::class, 'completeCryptoTransaction']);
Route::post('/merchant/card', [MerchantController::class, 'processCardPayment']);
Route::post('/merchant/crypto', [MerchantController::class, 'processCryptoPayment']);
