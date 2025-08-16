<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierStatusMovement extends Model
{
    protected $fillable = [
        'courier_id',
        'order_id',
        'status',
        'started_at',
        'ended_at',
        'duration_seconds',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Duration hesaplama
    public function getDurationInMinutesAttribute()
    {
        return $this->duration_seconds ? round($this->duration_seconds / 60, 2) : null;
    }
}

