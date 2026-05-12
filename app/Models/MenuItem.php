<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'label', 'url',
        'route_name', 'route_params', 'icon',
        'target', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'route_params' => 'array',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
                    ->orderBy('order')
                    ->where('is_active', true);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    /** Resolve the final URL (named route wins over static URL). */
    public function getResolvedUrlAttribute(): string
    {
        if ($this->route_name && Route::has($this->route_name)) {
            return route($this->route_name, $this->route_params ?? []);
        }

        return $this->url ?? '#';
    }

    /** Is this item the currently active page? */
    public function getIsCurrentAttribute(): bool
    {
        return request()->url() === $this->resolved_url
            || ($this->route_name && request()->routeIs($this->route_name));
    }
}