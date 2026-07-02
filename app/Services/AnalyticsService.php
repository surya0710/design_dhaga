<?php

namespace App\Services;

use App\Models\PageView;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function parseFilters(array $input): array
    {
        $period = $input['period'] ?? '30d';
        $dateTo = now()->toDateString();

        $dateFrom = match ($period) {
            '7d' => now()->subDays(6)->toDateString(),
            '90d' => now()->subDays(89)->toDateString(),
            'custom' => $input['date_from'] ?? now()->subDays(29)->toDateString(),
            default => now()->subDays(29)->toDateString(),
        };

        if ($period === 'custom') {
            $dateTo = $input['date_to'] ?? $dateTo;
            $dateFrom = $input['date_from'] ?? $dateFrom;
        }

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        return [
            'period' => $period,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'country' => $input['country'] ?? null,
            'utm_source' => null,
            'utm_medium' => null,
            'utm_campaign' => null,
            'browser' => null,
            'platform' => null,
            'path' => null,
            'user_type' => null,
        ];
    }

    public function periodLabel(string $period): string
    {
        return match ($period) {
            '7d' => 'Last 7 days',
            '90d' => 'Last 90 days',
            'custom' => 'Custom range',
            default => 'Last 30 days',
        };
    }

    public function friendlyPageName(string $path): string
    {
        $path = '/'.ltrim($path, '/');

        return match (true) {
            $path === '/' => 'Homepage',
            str_starts_with($path, '/product/') => 'Product page',
            str_starts_with($path, '/category/') => 'Category page',
            str_starts_with($path, '/blog/') => 'Blog post',
            str_starts_with($path, '/cart') => 'Cart',
            str_starts_with($path, '/checkout') => 'Checkout',
            default => $path,
        };
    }

    public function baseQuery(array $filters)
    {
        return PageView::query()->filtered($filters);
    }

    public function summary(array $filters): array
    {
        $query = $this->baseQuery($filters);

        $pageViews = (clone $query)->count();
        $uniqueVisitors = (clone $query)->distinct('visitor_id')->count('visitor_id');
        $loggedInViews = (clone $query)->whereNotNull('user_id')->count();
        $totalVisitorsAllTime = Visitor::count();
        $activeToday = Visitor::whereDate('visited_at', now()->toDateString())->count();

        $avgPagesPerVisitor = $uniqueVisitors > 0
            ? round($pageViews / $uniqueVisitors, 1)
            : 0;

        return [
            'page_views' => $pageViews,
            'unique_visitors' => $uniqueVisitors,
            'logged_in_views' => $loggedInViews,
            'avg_pages_per_visitor' => $avgPagesPerVisitor,
            'total_visitors_all_time' => $totalVisitorsAllTime,
            'active_today' => $activeToday,
        ];
    }

    public function pageViewsOverTime(array $filters): array
    {
        $from = Carbon::parse($filters['date_from']);
        $to = Carbon::parse($filters['date_to']);
        $days = $from->diffInDays($to);

        $groupFormat = $days <= 2 ? '%Y-%m-%d %H:00' : '%Y-%m-%d';
        $labelFormat = $days <= 2 ? 'M j, H:00' : 'M j';

        $rows = $this->baseQuery($filters)
            ->selectRaw("DATE_FORMAT(viewed_at, '{$groupFormat}') as period, COUNT(*) as views, COUNT(DISTINCT visitor_id) as visitors")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $labels = [];
        $views = [];
        $visitors = [];

        foreach ($rows as $row) {
            $labels[] = Carbon::createFromFormat($days <= 2 ? 'Y-m-d H:00' : 'Y-m-d', $row->period)->format($labelFormat);
            $views[] = (int) $row->views;
            $visitors[] = (int) $row->visitors;
        }

        return compact('labels', 'views', 'visitors');
    }

    public function topPages(array $filters, int $limit = 10): array
    {
        return $this->baseQuery($filters)
            ->select('path', DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT visitor_id) as visitors'))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'path' => $row->path,
                'views' => (int) $row->views,
                'visitors' => (int) $row->visitors,
            ])
            ->all();
    }

    public function topReferrers(array $filters, int $limit = 10): array
    {
        return $this->baseQuery($filters)
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->select('referrer', DB::raw('COUNT(*) as views'))
            ->groupBy('referrer')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'referrer' => $this->shortenReferrer($row->referrer),
                'views' => (int) $row->views,
            ])
            ->all();
    }

    public function breakdownByField(array $filters, string $field, int $limit = 10): array
    {
        return $this->baseQuery($filters)
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->select($field, DB::raw('COUNT(*) as views'))
            ->groupBy($field)
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'label' => $row->{$field},
                'views' => (int) $row->views,
            ])
            ->all();
    }

    public function filterOptions(): array
    {
        return [
            'countries' => PageView::whereNotNull('country')->distinct()->orderBy('country')->pluck('country'),
            'utm_sources' => PageView::whereNotNull('utm_source')->distinct()->orderBy('utm_source')->pluck('utm_source'),
            'utm_mediums' => PageView::whereNotNull('utm_medium')->distinct()->orderBy('utm_medium')->pluck('utm_medium'),
            'utm_campaigns' => PageView::whereNotNull('utm_campaign')->distinct()->orderBy('utm_campaign')->pluck('utm_campaign'),
            'browsers' => PageView::whereNotNull('browser')->distinct()->orderBy('browser')->pluck('browser'),
            'platforms' => PageView::whereNotNull('platform')->distinct()->orderBy('platform')->pluck('platform'),
        ];
    }

    public function recentPageViews(array $filters, int $limit = 25)
    {
        return $this->baseQuery($filters)
            ->with('user:id,name,email')
            ->orderByDesc('viewed_at')
            ->limit($limit)
            ->get();
    }

    private function shortenReferrer(string $referrer): string
    {
        $host = parse_url($referrer, PHP_URL_HOST);

        return $host ?: mb_substr($referrer, 0, 80);
    }
}
