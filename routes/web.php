<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', [PaymentController::class, 'showPaymentForm'])->name('pay');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('process.payment');
