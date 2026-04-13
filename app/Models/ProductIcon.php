<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductIcon extends Model
{
    protected $fillable = [
        'product_id',
        'image',
        'text'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
