<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'user_id',

        'ip',
        'user_agent',
        'browser',
        'platform',

        'referrer',

        'utm_source',
        'utm_medium',
        'utm_campaign',

        'country',
        'state',
        'city',

        'url',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (Useful for Reports)
    |--------------------------------------------------------------------------
    */

    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', now()->toDateString());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visited_at', now()->month)
                     ->whereYear('visited_at', now()->year);
    }

    public function scopeSource($query, $source)
    {
        return $query->where('utm_source', $source);
    }

    public function scopeCountry($query, $country)
    {
        return $query->where('country', $country);
    }
}