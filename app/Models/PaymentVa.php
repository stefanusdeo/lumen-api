<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentVa extends Model
{

    protected $table = "payment_va";
    protected $fillable = [
        'client_id',
        'reference_number',
        'virtual_account',
        'amount',
        'note'
    ];
}
