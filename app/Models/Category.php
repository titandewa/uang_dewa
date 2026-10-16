<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['user_id', 'name', 'percentage', 'balance', 'is_system'];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}
