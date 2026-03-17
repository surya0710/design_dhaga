<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    protected $fillable = [
        'email',
        'user_id',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
        'is_verified', 
        'verified_at', 
        'verification_token',
        'unsubscribe_token'
    ];
    protected $table = 'subscribes';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'N/A';
    }
}
