<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgressPaymentRecord extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['payable_type', 'payable_id', 'amount', 'payment_date', 'note'];


}
