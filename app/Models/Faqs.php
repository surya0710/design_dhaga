<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faqs extends Model
{
    protected $table = 'faqs';

    protected $fillable = [
        'page_slug',
        'question',
        'answer',
        'sort_order',
        'status',
    ];
}
