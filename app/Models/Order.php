<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',

        'name',
        'email',
        'phone',

        'country',
        'state',
        'city',
        'pincode',
        'address_line_1',
        'address_line_2',
        'landmark',
        'address_type',

        'notes',

        'subtotal',
        'shipping',
        'delivery_charge', // ✅ ADD
        'coupon_discount',
        'total',
        'gst_rate',
        'gst_type',
        'cgst_rate',
        'sgst_rate',
        'igst_rate',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'gst_amount',

        'delivery_type', // ✅ ADD
        'delivery_eta', // ✅ ADD
        'expected_delivery_date', // ✅ ADD
        'courier_name', // ✅ ADD
        'delivery_label', // ✅ ADD
        'shiprocket_courier_id', // ✅ ADD

        'coupon_code',
        'coupon_id',

        'payment_method',
        'payment_status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',

        'order_status',
        'paid_at',
        'delivered_at',
        'cancelled_at',
        'package_length',
        'package_breadth',
        'package_height',
        'package_weight',
        'awb_code',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'delivery_charge' => 'decimal:2', // ✅
        'coupon_discount' => 'decimal:2',
        'total' => 'decimal:2',
        'gst_rate' => 'decimal:2',
        'cgst_rate' => 'decimal:2',
        'sgst_rate' => 'decimal:2',
        'igst_rate' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',

        'expected_delivery_date' => 'date', // ✅

        'paid_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFullAddressAttribute()
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->landmark,
            $this->city,
            $this->state,
            $this->country,
            $this->pincode,
        ])->filter()->implode(', ');
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPending()
    {
        return $this->order_status === 'pending';
    }

    public function isDelivered()
    {
        return $this->order_status === 'delivered';
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
