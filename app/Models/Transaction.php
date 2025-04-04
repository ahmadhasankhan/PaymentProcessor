<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'address', 'zip_code', 'country', 'amount',
        'transaction_type', 'merchant_id', 'status'
    ];

    public function merchant()
    {
        return $this->belongsTo(merchant::Class);
    }
}
