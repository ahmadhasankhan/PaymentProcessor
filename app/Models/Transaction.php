<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'zip_code',
        'country',
        'amount',
        'transaction_type',
        'transaction_id',
        'merchant_id',
        'card_number',
        'expiration_date',
        'cvv',
        'wallet_address',
        'status',
        'error_message'
    ];

    public function merchant(){
        return $this->belongsTo(merchant::Class);
    }
}
