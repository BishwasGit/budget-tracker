<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address'
    ];

    public function sentTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_person_id');
    }

    public function receivedTransactions()
    {
        return $this->hasMany(Transaction::class, 'to_person_id');
    }
}
