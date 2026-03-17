<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'mobile',
        'country',
        'state',
        'city',
        'street',
        'pincode',
        'landmark',
        'address_type',
        'notes',
        'coupon_code',
        'coupon_id',
        'payment_id',
        'delivery_charge',
        'delivered_at',
        'coupon_discount',
        'payment_method',
        'total',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function getFullAddressAttribute()
    {
        return "{$this->street}, {$this->city}, {$this->state}, {$this->country} - {$this->pincode}";
    }
    public function getAddressTypeAttribute($value)
    {
        return ucfirst($value);
    }
}
