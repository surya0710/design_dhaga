<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTracking extends Model
{
    protected $fillable = [
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
    ];
}
