<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_id',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class); // Tek bir order ile ilişkiliyse
    }
}
