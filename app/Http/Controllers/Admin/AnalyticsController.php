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

        $summary = $this->analytics->summary($filters);
        $overTime = $this->analytics->pageViewsOverTime($filters);
        $topPages = collect($this->analytics->topPages($filters, 8))
            ->map(fn ($row) => array_merge($row, [
                'label' => $this->analytics->friendlyPageName($row['path']),
            ]))
            ->all();
        $topReferrers = $this->analytics->topReferrers($filters, 8);
        $countries = $this->analytics->breakdownByField($filters, 'country', 8);

        $ga4Configured = $this->ga4->isConfigured();
        $ga4ServiceAccountEmail = $this->ga4->serviceAccountEmail();
        $ga4Data = $ga4Configured
            ? $this->ga4->dashboardData($filters['date_from'], $filters['date_to'])
            : null;

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
