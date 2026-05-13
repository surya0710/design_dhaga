<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    protected $table = 'pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'meta_image',
        'status',
    ];
}
