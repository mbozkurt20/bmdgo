<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'restaurant_id',
        'customer_id',
        'courier_id',
        'tracking_id',
        'full_name',
        'phone',
        'payment_method',
        'items',
        'address',
        'status',
        'verify_code',
        'notes',
        'promotions',
        'coupon',
        'amount',
        'sub_amount',
        'message',
        'discount',
        'distance',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class, 'courier_id', 'id')->where('status', '!=',-1);
    }
}
