<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSystemFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_feature_id',
        'admin_id',
        'status',
    ];
}
