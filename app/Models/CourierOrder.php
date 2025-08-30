<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourierOrder extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'courier_id',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class); // Tek bir order ile ilişkiliyse
    }
}
