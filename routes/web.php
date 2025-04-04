<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', [PaymentController::class, 'showPaymentForm'])->name('pay');
Route::post('/payment', [PaymentController::class, 'process'])->name('process.payment');
