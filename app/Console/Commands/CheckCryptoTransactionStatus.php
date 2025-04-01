<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CryptoPaymentService;

class CheckCryptoTransactionStatus extends Command
{
    protected $signature = 'crypto:check-transactions'; // Command name
    protected $description = 'Check the status of pending crypto transactions';

    protected $cryptoPaymentService;

    public function __construct(CryptoPaymentService $cryptoPaymentService)
    {
        parent::__construct();
        $this->cryptoPaymentService = $cryptoPaymentService;
    }

    public function handle()
    {
        $this->cryptoPaymentService->checkPendingTransactions();
        $this->info('Checked pending crypto transactions.');
    }
}
