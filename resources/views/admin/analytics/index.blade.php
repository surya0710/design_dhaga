@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <div>
                <h3>Website Traffic</h3>
                <p class="body-text mb-0">Overview from Google Analytics.</p>
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

        @if(!$ga4Configured)
            <div class="wg-box mb-20">
                <h5 class="mb-10">Google Analytics not configured</h5>
                @if(count($ga4Issues))
                    <ul class="body-text mb-15">
                        @foreach($ga4Issues as $issue)
                            <li>{{ $issue }}</li>
                        @endforeach
                    </ul>
                @endif

                <p class="body-text mb-10"><strong>Step 1 — Install the PHP package on the server</strong></p>
                <pre class="body-text mb-15" style="background:#f5f5f5;padding:12px;border-radius:6px;"><code>cd /home/u255538404/domains/designdhaga.com/public_html
composer install --no-dev --optimize-autoloader
php artisan config:clear</code></pre>

                <p class="body-text mb-10"><strong>Step 2 — Add credentials (choose one option)</strong></p>
                <p class="body-text mb-5">Option A: Upload the JSON file via FTP/File Manager to:</p>
                <pre class="body-text mb-10" style="background:#f5f5f5;padding:12px;border-radius:6px;"><code>storage/app/design-dhaga-24f939f64666.json</code></pre>

                <p class="body-text mb-5">Option B: Put the credentials in <code>.env</code> as base64 (no file upload needed). On your local machine run:</p>
                <pre class="body-text mb-10" style="background:#f5f5f5;padding:12px;border-radius:6px;"><code>php -r "echo base64_encode(file_get_contents('storage/app/design-dhaga-24f939f64666.json'));"</code></pre>
                <p class="body-text mb-5">Then add the output to the server <code>.env</code>:</p>
                <pre class="body-text mb-0" style="background:#f5f5f5;padding:12px;border-radius:6px;"><code>GA4_PROPERTY_ID=505923248
GA4_CREDENTIALS=storage/app/design-dhaga-24f939f64666.json
GA4_CREDENTIALS_BASE64=paste_the_base64_string_here</code></pre>
            </div>
        @elseif($ga4Data && !empty($ga4Data['error']))
            <div class="wg-box mb-20">
                <h5 class="mb-10">Google Analytics</h5>
                @if(str_contains($ga4Data['error'], 'PERMISSION_DENIED') || str_contains($ga4Data['error'], 'sufficient permissions'))
                    <p class="body-text mb-2">Google Analytics is almost connected. Add this email as a <strong>Viewer</strong> in GA4 Admin → Property access management:</p>
                    <p class="body-text mb-0"><code>{{ $ga4ServiceAccountEmail }}</code></p>
                @else
                    <p class="body-text mb-0">Google Analytics could not be loaded right now.</p>
                @endif
            </div>
        @elseif($summary)
            <div class="flex gap20 flex-wrap-mobile mb-30">
                <div class="w-half">
                    <div class="wg-chart-default mb-20">
                        <div class="body-text mb-2">Page views</div>
                        <h4>{{ number_format($summary['page_views'] ?? 0) }}</h4>
                        <div class="body-text">Total pages viewed</div>
                    </div>
                </div>
                <div class="w-half">
                    <div class="wg-chart-default mb-20">
                        <div class="body-text mb-2">Active users</div>
                        <h4>{{ number_format($summary['active_users'] ?? 0) }}</h4>
                        <div class="body-text">Unique visitors</div>
                    </div>
                </div>
                <div class="w-half">
                    <div class="wg-chart-default mb-20">
                        <div class="body-text mb-2">Sessions</div>
                        <h4>{{ number_format($summary['sessions'] ?? 0) }}</h4>
                        <div class="body-text">Total visits</div>
                    </div>
                </div>
                <div class="w-half">
                    <div class="wg-chart-default mb-20">
                        <div class="body-text mb-2">New users</div>
                        <h4>{{ number_format($summary['new_users'] ?? 0) }}</h4>
                        <div class="body-text">First-time visitors</div>
                    </div>
                </div>
            </div>

            <div class="wg-box mb-20">
                <h5 class="mb-20">Traffic over time</h5>
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
                                            <th class="text-end">Views</th>
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
                            <p class="body-text mb-0">No page views for this period.</p>
                        @endif
                    </div>
                </div>
                <div class="w-half">
                    <div class="wg-box">
                        <h5 class="mb-20">Users by country</h5>
                        @if(count($countries))
                            <div id="chart-countries"></div>
                            <div class="wg-table table-all-user mt-3">
                                <table class="table table-striped table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Country</th>
                                            <th class="text-end">Users</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($countries as $row)
                                            <tr>
                                                <td>{{ $row['label'] }}</td>
                                                <td class="text-end">{{ number_format($row['users']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="body-text mb-0">No country data for this period.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="wg-box mb-20">
                <h5 class="mb-20">Traffic sources</h5>
                @if(count($topReferrers))
                    <div class="wg-table table-all-user">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Source / medium</th>
                                    <th class="text-end">Sessions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topReferrers as $row)
                                    <tr>
                                        <td>{{ $row['referrer'] }}</td>
                                        <td class="text-end">{{ number_format($row['sessions']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="body-text mb-0">No traffic source data for this period.</p>
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
        @if($summary)
        renderLineChart('#chart-traffic-over-time', @json($overTime['labels']), [
            { name: 'Sessions', data: @json($overTime['sessions']) },
            { name: 'Users', data: @json($overTime['users']) },
        ]);

        renderDonut('#chart-countries',
            @json(array_column($countries, 'label')),
            @json(array_column($countries, 'users'))
        );
        @endif
    });
})(jQuery);
</script>
@endpush
