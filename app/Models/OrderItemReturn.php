<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemReturn extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'reason_title',
        'reason',
        'status',

    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function getStatusAttribute($value)
    {
        return ucfirst($value);
    }
}
