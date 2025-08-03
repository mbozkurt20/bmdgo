<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quarter extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'district_id',
        'semt_id',
        'name',
    ];
}
