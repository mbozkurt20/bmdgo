<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'platform',
        'restaurant_id',
        'customer_id',
        'courier_id',
        'tracking_id',
        'full_name',
        'phone',
        'pid',
        'entegra_current_status', // entegra old.stataus
        'entegra_next_status', // entegra next status
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
        'platform_date',
        'created_at',
        'assigned_at',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class, 'courier_id', 'id')->where('status', '!=',-1);
    }

    public function logs()
    {
        return $this->hasMany(OrderStatusLog::class, 'order_id', 'id');
    }

    public static function paymentMethods()
    {
        return [
            'nakit'         => 'Kapıda Nakit ile Ödeme',
            'kredi_karti'   => 'Kapıda Kredi Kartı ile Ödeme',
            'ticket'        => 'Kapıda Ticket ile Ödeme',
            'sodexo'        => 'Kapıda Sodexo ile Ödeme',
            'multinet'      => 'Kapıda Multinet ile Ödeme',
            'pluxee'        => 'Kapıda Pluxee ile Ödeme',
            'online'        => 'Online Kredi/Banka Kartı'
        ];
    }
}
