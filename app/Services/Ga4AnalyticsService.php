<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Ga4AnalyticsService
{
    public function isConfigured(): bool
    {
        if (! class_exists(BetaAnalyticsDataClient::class)) {
            return false;
        }

        $propertyId = config('analytics.ga4_property_id');
        $credentials = $this->credentialsPath();

        return $propertyId && $credentials && is_readable($credentials);
    }

    private function credentialsPath(): string
    {
        return (string) config('analytics.ga4_credentials');
    }

    public function serviceAccountEmail(): ?string
    {
        $path = $this->credentialsPath();

        if (! is_readable($path)) {
            return null;
        }

        $json = json_decode((string) file_get_contents($path), true);

        return $json['client_email'] ?? null;
    }

    public function dashboardData(string $dateFrom, string $dateTo): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $cacheKey = 'ga4_dashboard_'.md5($dateFrom.$dateTo);

        $cached = Cache::get($cacheKey);

        if (is_array($cached) && empty($cached['error'])) {
            return $cached;
        }

        try {
            $data = [
                'summary' => $this->fetchSummary($dateFrom, $dateTo),
                'over_time' => $this->fetchOverTime($dateFrom, $dateTo),
                'top_pages' => $this->fetchTopPages($dateFrom, $dateTo),
                'sources' => $this->fetchSources($dateFrom, $dateTo),
                'countries' => $this->fetchCountries($dateFrom, $dateTo),
                'devices' => $this->fetchDevices($dateFrom, $dateTo),
            ];

            Cache::put($cacheKey, $data, now()->addMinutes(config('analytics.ga4_cache_minutes')));

            return $data;
        } catch (\Throwable $e) {
            Log::warning('GA4 Data API error: '.$e->getMessage());

            return ['error' => $e->getMessage()];
        }
    }

    private function client(): BetaAnalyticsDataClient
    {
        return new BetaAnalyticsDataClient([
            'credentials' => $this->credentialsPath(),
        ]);
    }

    private function property(): string
    {
        return 'properties/'.config('analytics.ga4_property_id');
    }

    private function dateRange(string $from, string $to): array
    {
        return [
            (new DateRange())
                ->setStartDate($from)
                ->setEndDate($to),
        ];
    }

    private function fetchSummary(string $dateFrom, string $dateTo): array
    {
        $response = $this->client()->runReport(new RunReportRequest([
            'property' => $this->property(),
            'date_ranges' => $this->dateRange($dateFrom, $dateTo),
            'metrics' => [
                new Metric(['name' => 'activeUsers']),
                new Metric(['name' => 'sessions']),
                new Metric(['name' => 'screenPageViews']),
                new Metric(['name' => 'averageSessionDuration']),
                new Metric(['name' => 'bounceRate']),
                new Metric(['name' => 'newUsers']),
            ],
        ]));

        $row = $response->getRows()[0] ?? null;

        if (! $row) {
            return [];
        }

        $values = $row->getMetricValues();

        return [
            'active_users' => (int) ($values[0]->getValue() ?? 0),
            'sessions' => (int) ($values[1]->getValue() ?? 0),
            'page_views' => (int) ($values[2]->getValue() ?? 0),
            'avg_session_duration' => round((float) ($values[3]->getValue() ?? 0)),
            'bounce_rate' => round((float) ($values[4]->getValue() ?? 0) * 100, 1),
            'new_users' => (int) ($values[5]->getValue() ?? 0),
        ];
    }

    private function fetchOverTime(string $dateFrom, string $dateTo): array
    {
        $response = $this->client()->runReport(new RunReportRequest([
            'property' => $this->property(),
            'date_ranges' => $this->dateRange($dateFrom, $dateTo),
            'dimensions' => [new Dimension(['name' => 'date'])],
            'metrics' => [
                new Metric(['name' => 'sessions']),
                new Metric(['name' => 'activeUsers']),
            ],
        ]));

        $rows = [];

        foreach ($response->getRows() as $row) {
            $date = $row->getDimensionValues()[0]->getValue();
            $rows[] = [
                'date' => $date,
                'label' => substr($date, 4, 2).'/'.substr($date, 6, 2),
                'sessions' => (int) $row->getMetricValues()[0]->getValue(),
                'users' => (int) $row->getMetricValues()[1]->getValue(),
            ];
        }

        usort($rows, fn ($a, $b) => $a['date'] <=> $b['date']);

        return [
            'labels' => array_column($rows, 'label'),
            'sessions' => array_column($rows, 'sessions'),
            'users' => array_column($rows, 'users'),
        ];
    }

    private function fetchTopPages(string $dateFrom, string $dateTo, int $limit = 10): array
    {
        $response = $this->client()->runReport(new RunReportRequest([
            'property' => $this->property(),
            'date_ranges' => $this->dateRange($dateFrom, $dateTo),
            'dimensions' => [new Dimension(['name' => 'pagePath'])],
            'metrics' => [new Metric(['name' => 'screenPageViews'])],
            'limit' => $limit,
        ]));

        $pages = [];

        foreach ($response->getRows() as $row) {
            $pages[] = [
                'path' => $row->getDimensionValues()[0]->getValue(),
                'views' => (int) $row->getMetricValues()[0]->getValue(),
            ];
        }

        usort($pages, fn ($a, $b) => $b['views'] <=> $a['views']);

        return array_slice($pages, 0, $limit);
    }

    private function fetchSources(string $dateFrom, string $dateTo, int $limit = 10): array
    {
        $response = $this->client()->runReport(new RunReportRequest([
            'property' => $this->property(),
            'date_ranges' => $this->dateRange($dateFrom, $dateTo),
            'dimensions' => [
                new Dimension(['name' => 'sessionSource']),
                new Dimension(['name' => 'sessionMedium']),
            ],
            'metrics' => [new Metric(['name' => 'sessions'])],
            'limit' => $limit,
        ]));

        $sources = [];

        foreach ($response->getRows() as $row) {
            $source = $row->getDimensionValues()[0]->getValue();
            $medium = $row->getDimensionValues()[1]->getValue();
            $sources[] = [
                'label' => $source.' / '.$medium,
                'sessions' => (int) $row->getMetricValues()[0]->getValue(),
            ];
        }

        usort($sources, fn ($a, $b) => $b['sessions'] <=> $a['sessions']);

        return array_slice($sources, 0, $limit);
    }

    private function fetchCountries(string $dateFrom, string $dateTo, int $limit = 10): array
    {
        $response = $this->client()->runReport(new RunReportRequest([
            'property' => $this->property(),
            'date_ranges' => $this->dateRange($dateFrom, $dateTo),
            'dimensions' => [new Dimension(['name' => 'country'])],
            'metrics' => [new Metric(['name' => 'activeUsers'])],
            'limit' => $limit,
        ]));

        $countries = [];

        foreach ($response->getRows() as $row) {
            $countries[] = [
                'label' => $row->getDimensionValues()[0]->getValue(),
                'users' => (int) $row->getMetricValues()[0]->getValue(),
            ];
        }

        usort($countries, fn ($a, $b) => $b['users'] <=> $a['users']);

        return array_slice($countries, 0, $limit);
    }

    private function fetchDevices(string $dateFrom, string $dateTo): array
    {
        $response = $this->client()->runReport(new RunReportRequest([
            'property' => $this->property(),
            'date_ranges' => $this->dateRange($dateFrom, $dateTo),
            'dimensions' => [new Dimension(['name' => 'deviceCategory'])],
            'metrics' => [new Metric(['name' => 'sessions'])],
        ]));

        $devices = [];

        foreach ($response->getRows() as $row) {
            $devices[] = [
                'label' => ucfirst($row->getDimensionValues()[0]->getValue()),
                'sessions' => (int) $row->getMetricValues()[0]->getValue(),
            ];
        }

        usort($devices, fn ($a, $b) => $b['sessions'] <=> $a['sessions']);

        return $devices;
    }
}
