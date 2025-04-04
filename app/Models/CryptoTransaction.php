<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id', 'wallet_address', 'transaction_hash', 'status'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}

