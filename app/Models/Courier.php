<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'restaurant_id',
        'name',
        'phone',
        'email',
        'status',
        'situation',
        'price',
    ];
}
