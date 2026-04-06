<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'country',
        'state',
        'city',
        'pincode',
        'address_line_1',
        'address_line_2',
        'landmark',
        'address_type',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
}