<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'amount',
        'note',
        'transaction_date',
        'group_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];
}
