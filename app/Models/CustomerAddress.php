<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'customer_id',
        'name',
        'sokak_cadde',
        'bina_no',
        'kat',
        'daire_no',
        'mahalle',
        'adres_tarifi',
        'status',
        'city_id',
        'district_id',
    ];
}
