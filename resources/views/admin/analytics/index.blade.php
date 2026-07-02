@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <div>
                <h3>Website Traffic</h3>
                <p class="body-text mb-0">Simple overview of who is visiting your store.</p>
            </div>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Analytics</div></li>
            </ul>
        </div>

        <div class="wg-box mb-20">
            <form method="GET" action="{{ route('admin.analytics') }}" class="form-style-1">
                <div class="flex items-center flex-wrap gap10 mb-3">
                    @foreach(['7d' => '7 days', '30d' => '30 days', '90d' => '90 days'] as $key => $label)
                        <button type="submit" name="period" value="{{ $key }}"
                            class="tf-button {{ ($filters['period'] ?? '30d') === $key ? '' : 'style-2' }} w-auto">
                            {{ $label }}
                        </button>
                    @endforeach
                    <button type="button" id="toggle-custom-dates"
                        class="tf-button {{ ($filters['period'] ?? '') === 'custom' ? '' : 'style-2' }} w-auto">
                        Custom dates
                    </button>
                </div>

                <div id="custom-date-fields" class="row g-3 {{ ($filters['period'] ?? '') === 'custom' ? '' : 'd-none' }}">
                    <input type="hidden" name="period" value="custom">
                    <div class="col-md-3">
                        <label class="body-text mb-2 d-block">From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                    </div>
                    <div class="col-md-3">
                        <label class="body-text mb-2 d-block">To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="tf-button w-auto">Apply</button>
                    </div>
                </div>
            </form>
            <p class="body-text mb-0 mt-2">Showing data for <strong>{{ $periodLabel }}</strong>
                ({{ \Carbon\Carbon::parse($filters['date_from'])->format('M j, Y') }}
                – {{ \Carbon\Carbon::parse($filters['date_to'])->format('M j, Y') }})</p>
        </div>

        <div class="flex gap20 flex-wrap-mobile mb-30">
            <div class="w-half">
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Total visits</div>
                    <h4>{{ number_format($summary['page_views']) }}</h4>
                    <div class="body-text">How many pages were opened</div>
                </div>
            </div>
            <div class="w-half">
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Unique visitors</div>
                    <h4>{{ number_format($summary['unique_visitors']) }}</h4>
                    <div class="body-text">Different people who visited</div>
                </div>
            </div>
            <div class="w-half">
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Pages per visitor</div>
                    <h4>{{ $summary['avg_pages_per_visitor'] }}</h4>
                    <div class="body-text">Average browsing depth</div>
                </div>
            </div>
            <div class="w-half">
                <div class="wg-chart-default mb-20">
                    <div class="body-text mb-2">Visitors today</div>
                    <h4>{{ number_format($summary['active_today']) }}</h4>
                    <div class="body-text">People active so far today</div>
                </div>
            </div>
        </div>

        <div class="wg-box mb-20">
            <h5 class="mb-20">Visits over time</h5>
            <div id="chart-traffic-over-time"></div>
        </div>

        <div class="flex gap20 flex-wrap-mobile mb-30">
            <div class="w-half">
                <div class="wg-box">
                    <h5 class="mb-20">Most visited pages</h5>
                    @if(count($topPages))
                        <div class="wg-table table-all-user">
                            <table class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Page</th>
                                        <th class="text-end">Visits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPages as $row)
                                        <tr>
                                            <td>
                                                <div>{{ $row['label'] }}</div>
                                                <div class="body-text">{{ $row['path'] }}</div>
                                            </td>
                                            <td class="text-end">{{ number_format($row['views']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="body-text mb-0">No page visits yet for this period.</p>
                    @endif
                </div>
            </div>
            <div class="w-half">
                <div class="wg-box">
                    <h5 class="mb-20">Visitors by country</h5>
                    @if(count($countries))
                        <div id="chart-countries"></div>
                        <div class="wg-table table-all-user mt-3">
                            <table class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th class="text-end">Visits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($countries as $row)
                                        <tr>
                                            <td>{{ $row['label'] }}</td>
                                            <td class="text-end">{{ number_format($row['views']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="body-text mb-0">No country data yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="wg-box mb-20">
            <h5 class="mb-20">Where visitors came from</h5>
            @if(count($topReferrers))
                <div class="wg-table table-all-user">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Website / source</th>
                                <th class="text-end">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topReferrers as $row)
                                <tr>
                                    <td>{{ $row['referrer'] }}</td>
                                    <td class="text-end">{{ number_format($row['views']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="body-text mb-0">Visitors came directly or no referrer was recorded.</p>
            @endif
        </div>

        @if($ga4Configured && $ga4Data && empty($ga4Data['error']))
            <div class="wg-box mb-20">
                <h5 class="mb-10">Google Analytics</h5>
                <p class="body-text mb-20">Extra stats pulled from your Google Analytics account.</p>

                <div class="flex gap20 flex-wrap-mobile mb-20">
                    <div class="w-half">
                        <div class="wg-chart-default">
                            <div class="body-text mb-2">Google — active users</div>
                            <h4>{{ number_format($ga4Data['summary']['active_users'] ?? 0) }}</h4>
                        </div>
                    </div>
                    <div class="w-half">
                        <div class="wg-chart-default">
                            <div class="body-text mb-2">Google — page views</div>
                            <h4>{{ number_format($ga4Data['summary']['page_views'] ?? 0) }}</h4>
                        </div>
                    </div>
                    <div class="w-half">
                        <div class="wg-chart-default">
                            <div class="body-text mb-2">Google — new users</div>
                            <h4>{{ number_format($ga4Data['summary']['new_users'] ?? 0) }}</h4>
                        </div>
                    </div>
                    <div class="w-half">
                        <div class="wg-chart-default">
                            <div class="body-text mb-2">Google — bounce rate</div>
                            <h4>{{ $ga4Data['summary']['bounce_rate'] ?? 0 }}%</h4>
                        </div>
                    </div>
                </div>

                <div id="chart-ga4-over-time"></div>
            </div>
        @elseif($ga4Configured && !empty($ga4Data['error']))
            <div class="wg-box mb-20">
                <h5 class="mb-10">Google Analytics</h5>
                @if(str_contains($ga4Data['error'], 'PERMISSION_DENIED') || str_contains($ga4Data['error'], 'sufficient permissions'))
                    <p class="body-text mb-2">Google Analytics is almost connected. Add this email as a <strong>Viewer</strong> in GA4 Admin → Property access management:</p>
                    <p class="body-text mb-0"><code>{{ $ga4ServiceAccountEmail }}</code></p>
                @else
                    <p class="body-text mb-0">Google Analytics could not be loaded right now.</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function($) {
    $('#toggle-custom-dates').on('click', function() {
        $('#custom-date-fields').toggleClass('d-none');
    });

    function renderLineChart(el, labels, series) {
        if (!document.querySelector(el) || !labels.length) return;
        new ApexCharts(document.querySelector(el), {
            series: series,
            chart: { type: 'area', height: 300, toolbar: { show: false } },
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false },
            legend: { position: 'bottom' },
            xaxis: { categories: labels },
            colors: ['#2377FC', '#22C55E'],
        }).render();
    }

    function renderDonut(el, labels, data) {
        if (!document.querySelector(el) || !labels.length) return;
        new ApexCharts(document.querySelector(el), {
            series: data,
            labels: labels,
            chart: { type: 'donut', height: 260 },
            legend: { position: 'bottom' },
            colors: ['#2377FC', '#22C55E', '#FFA500', '#EF4444', '#8B5CF6', '#06B6D4'],
        }).render();
    }

    $(window).on('load', function() {
        renderLineChart('#chart-traffic-over-time', @json($overTime['labels']), [
            { name: 'Visits', data: @json($overTime['views']) },
            { name: 'Visitors', data: @json($overTime['visitors']) },
        ]);

        renderDonut('#chart-countries',
            @json(array_column($countries, 'label')),
            @json(array_column($countries, 'views'))
        );

        @if($ga4Configured && $ga4Data && empty($ga4Data['error']))
        renderLineChart('#chart-ga4-over-time', @json($ga4Data['over_time']['labels'] ?? []), [
            { name: 'Sessions', data: @json($ga4Data['over_time']['sessions'] ?? []) },
        ]);
        @endif
    });
})(jQuery);
</script>
@endpush
