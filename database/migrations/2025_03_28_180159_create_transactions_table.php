<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->text('address');
            $table->string('zip_code');
            $table->string('country');
            $table->decimal('amount', 10, 2);
            $table->enum('transaction_type', ['deposit', 'withdrawal']);
            $table->foreignId('merchant_id')->index();
            $table->string('status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('wallet_address')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
