<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id', 'card_number', 'expiry_date', 'cvv'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
