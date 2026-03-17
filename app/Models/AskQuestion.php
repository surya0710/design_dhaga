<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AskQuestion extends Model
{
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'message',
        'product_id',
        'product_name',
        'user_id'
    ];
    protected $table = 'ask_questions';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getProductNameAttribute()
    {
        return $this->product ? $this->product->name : 'N/A';
    }
}
