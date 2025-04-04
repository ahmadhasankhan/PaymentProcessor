<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/', [TransactionController::class, 'showPaymentForm'])->name('pay');
Route::post('/transaction', [TransactionController::class, 'process'])->name('process.payment');
