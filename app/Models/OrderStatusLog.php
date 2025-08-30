<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'restaurant_id',
        'status',
        'duration_seconds',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];
}
