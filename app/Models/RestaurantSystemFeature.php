<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantSystemFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_feature_id',
        'restaurant_id',
        'status',
    ];
}
