<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Root-level active items, ordered. */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->where('is_active', true);
    }

    /** All items (for admin). */
    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    /** Retrieve a menu by slug with nested items eager-loaded. */
    public static function getBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)
                     ->where('is_active', true)
                     ->with('items.children')
                     ->first();
    }
    
}