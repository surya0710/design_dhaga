<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageHighlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'emoji',
        'alt_text',
        'sort_order',
        'status'
    ];
}