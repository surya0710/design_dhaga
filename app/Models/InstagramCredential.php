<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramCredential extends Model
{
    protected $fillable = [
        'access_token',
        'user_id',
        'page_id',
        'expires_at',
        'last_refreshed_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_refreshed_at' => 'datetime',
        ];
    }
}
