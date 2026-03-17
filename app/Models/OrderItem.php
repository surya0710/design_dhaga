<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'status',
        'product_sku',
        'product_category',
        'certificate_name',
        'certificate_price',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function returnRequest()
    {
        return $this->hasOne(OrderItemReturn::class);
    }
}
