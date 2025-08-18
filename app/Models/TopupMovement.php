<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopupMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'top_up_price',
        'total_amount',
        'top_up',
        'type',
        'created_type',
        'is_approved',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];
}
