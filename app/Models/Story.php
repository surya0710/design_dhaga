<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $fillable = [
        'year',
        'description',
        'image',
        'display_order',
        'status',
    ];
}   