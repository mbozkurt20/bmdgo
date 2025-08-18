<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'phone',
        'mobile',
        'status',
    ];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }
}
