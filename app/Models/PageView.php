<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'visitor_id',
        'user_id',
        'path',
        'url',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'country',
        'state',
        'city',
        'browser',
        'platform',
        'ip',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFiltered(Builder $query, array $filters): Builder
    {
        if (! empty($filters['date_from'])) {
            $query->where('viewed_at', '>=', $filters['date_from'].' 00:00:00');
        }

        if (! empty($filters['date_to'])) {
            $query->where('viewed_at', '<=', $filters['date_to'].' 23:59:59');
        }

        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (! empty($filters['utm_source'])) {
            $query->where('utm_source', $filters['utm_source']);
        }

        if (! empty($filters['utm_medium'])) {
            $query->where('utm_medium', $filters['utm_medium']);
        }

        if (! empty($filters['utm_campaign'])) {
            $query->where('utm_campaign', $filters['utm_campaign']);
        }

        if (! empty($filters['browser'])) {
            $query->where('browser', $filters['browser']);
        }

        if (! empty($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }

        if (! empty($filters['path'])) {
            $query->where('path', 'like', '%'.$filters['path'].'%');
        }

        if (($filters['user_type'] ?? '') === 'logged_in') {
            $query->whereNotNull('user_id');
        } elseif (($filters['user_type'] ?? '') === 'guest') {
            $query->whereNull('user_id');
        }

        return $query;
    }
}
