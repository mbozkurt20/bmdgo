<?php

namespace App\Models;

use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Courier extends Authenticatable implements JWTSubject
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'admin_id',
        'restaurant_id',
        'name',
        'phone',
        'status',
        'password',
        'last_assigned_at',
        'latitude',
        'longitude',
        'price_type',
        'price', //paket başı
        'km_price',
        'fixed_price',
        'code',
        'birthday',
        'point',
        'day_point',
        'is_active',
        'fcm_token',
    ];

    public function payments()
    {
        return $this->morphMany(ProgressPaymentRecord::class, 'payable');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
