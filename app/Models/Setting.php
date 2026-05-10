<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'logo',
        'dark_logo',
        'favicon',
        'store_name',
        'office_address',
        'store_address',
        'support_email',
        'contact_number',
        'whatsapp_number',
        'working_days',
        'opening_time',
        'closing_time',
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
        'youtube',
        'meta_title',
        'meta_description',
    ];
}