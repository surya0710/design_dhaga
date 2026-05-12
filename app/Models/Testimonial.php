<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'testimonial', 'stars', 'image','status'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
