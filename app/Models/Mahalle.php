<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahalle extends Model
{
    protected $table = 'mahalleler'; // Tablo adını doğru ayarladığınızdan emin olun
    protected $fillable = ['ilce_id', 'mahalle_adi'];
}