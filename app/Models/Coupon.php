<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_cart_value', 'max_discount',
        'start_date', 'end_date', 'is_single_use', 'status'
    ];
    
}
