<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory,SoftDeletes;

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
