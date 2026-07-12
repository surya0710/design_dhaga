<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\Ga4AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analytics,
        private Ga4AnalyticsService $ga4,
    ) {}

    public function index(Request $request)
    {
        $filters = $this->analytics->parseFilters($request->all());
        $periodLabel = $this->analytics->periodLabel($filters['period']);

        $ga4Configured = $this->ga4->isConfigured();
        $ga4ServiceAccountEmail = $this->ga4->serviceAccountEmail();
        $ga4Data = $ga4Configured
            ? $this->ga4->dashboardData($filters['date_from'], $filters['date_to'])
            : null;

        $summary = null;
        $overTime = ['labels' => [], 'sessions' => [], 'users' => []];
        $topPages = [];
        $topReferrers = [];
        $countries = [];

        if ($ga4Data && empty($ga4Data['error'])) {
            $summary = $ga4Data['summary'];
            $overTime = $ga4Data['over_time'];
            $topPages = collect($ga4Data['top_pages'] ?? [])
                ->map(fn ($row) => array_merge($row, [
                    'label' => $this->analytics->friendlyPageName($row['path']),
                ]))
                ->all();
            $topReferrers = array_map(fn ($row) => [
                'referrer' => $row['label'],
                'sessions' => $row['sessions'],
            ], $ga4Data['sources'] ?? []);
            $countries = $ga4Data['countries'] ?? [];
        }

        return view('admin.analytics.index', compact(
            'filters',
            'periodLabel',
            'summary',
            'overTime',
            'topPages',
            'topReferrers',
            'countries',
            'ga4Configured',
            'ga4ServiceAccountEmail',
            'ga4Data',
        ));
    }
}
