<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = [
        'current_balance',
        'total_to_pay',
        'total_to_receive'
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'total_to_pay' => 'decimal:2',
        'total_to_receive' => 'decimal:2'
    ];
}
