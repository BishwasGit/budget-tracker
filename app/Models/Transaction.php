<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'paid_amount',
        'remaining_amount',
        'description',
        'status',
        'from_person_id',
        'to_person_id',
        'parent_transaction_id',
        'user_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2'
    ];

    public function fromPerson()
    {
        return $this->belongsTo(Person::class, 'from_person_id');
    }

    public function toPerson()
    {
        return $this->belongsTo(Person::class, 'to_person_id');
    }

    public function parentTransaction()
    {
        return $this->belongsTo(Transaction::class, 'parent_transaction_id');
    }

    public function childTransactions()
    {
        return $this->hasMany(Transaction::class, 'parent_transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
