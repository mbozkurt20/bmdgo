<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Restaurant extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'restaurant_code',
        'restaurant_name',
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'tax_name',
        'tax_number',
        'address',
        'status',
        'yemeksepeti_email',
        'yemeksepeti_password',
        'yemeksepeti_token',
        'yemeksepeti_tarih',
        'adisyo_api_key',
        'adisyo_secret_key',
        'adisyo_consumer_adi',
        'remember_token',
        'getir_restaurant_id',
        'getir_app_secret_key',
        'getir_restaurant_secret_key',
        'getir_token',
        'getir_tarih',
        'trendyol_satici_id',
        'trendyol_sube_id',
        'trendyol_api_key',
        'trendyol_secret_key',
        'entegra_id',
        'entegra_token',
        'package_price',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders(): HasMany{
        return $this->hasMany(Order::class, 'restaurant_id');
    }
}
