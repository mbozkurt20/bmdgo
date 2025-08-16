<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'coupon_id',
        'name',
        'description',
        'total_seller_amount',
    ];
}
