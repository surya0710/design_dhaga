<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model {
    protected $fillable = ['title','slug', 'content', 'status', 'image', 'meta_title', 'meta_keywords', 'meta_description'];

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }
}
