<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\PageView;
use App\Models\Visitor;

class TrackUser
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $agent = new Agent();

        $visitorId = $this->resolveVisitorId($request);

        $ip = $request->ip();
        $geo = [];

        if (!$this->isLocalIp($ip) && !app()->environment('testing')) {
            $geo = Cache::remember("geoip:{$ip}", now()->addDay(), function () use ($ip) {
                try {
                    $response = Http::timeout(1)->get("http://ip-api.com/json/{$ip}");

                    return $response->successful() ? $response->json() : [];
                } catch (\Throwable) {
                    return [];
                }
            });
        }

        $browser = $agent->browser();
        $platform = $agent->platform();
        $referrer = $request->headers->get('referer');
        $utmSource = $request->query('utm_source');
        $utmMedium = $request->query('utm_medium');
        $utmCampaign = $request->query('utm_campaign');
        $country = $geo['country'] ?? null;
        $state = $geo['regionName'] ?? null;
        $city = $geo['city'] ?? null;
        $now = now();

        Visitor::updateOrCreate(
            ['visitor_id' => $visitorId],
            [
                'user_id' => auth()->id(),
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'browser' => $browser,
                'platform' => $platform,
                'referrer' => $referrer,
                'utm_source' => $utmSource,
                'utm_medium' => $utmMedium,
                'utm_campaign' => $utmCampaign,
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'url' => $request->fullUrl(),
                'visited_at' => $now,
            ]
        );

        PageView::create([
            'visitor_id' => $visitorId,
            'user_id' => auth()->id(),
            'path' => '/'.ltrim($request->path(), '/'),
            'url' => $request->fullUrl(),
            'referrer' => $referrer,
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'country' => $country,
            'state' => $state,
            'city' => $city,
            'browser' => $browser,
            'platform' => $platform,
            'ip' => $ip,
            'viewed_at' => $now,
        ]);

        return $next($request);
    }

    private function resolveVisitorId(Request $request): string
    {
        $visitorId = $request->cookie('visitor_id');

        if (! $visitorId || ! Str::isUuid($visitorId)) {
            $visitorId = (string) Str::uuid();
            cookie()->queue(cookie('visitor_id', $visitorId, 60 * 24 * 365));
        }

        return $visitorId;
    }

    private function shouldSkip(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return true;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return true;
        }

        $path = $request->path();

        if (str_starts_with($path, 'admin')) {
            return true;
        }

        if (preg_match('/\.(css|js|map|ico|png|jpe?g|gif|webp|svg|woff2?|ttf|eot|pdf|xml|txt)$/i', $path)) {
            return true;
        }

        $skipPrefixes = ['livewire', 'storage', 'build', 'api', 'webhook', 'razorpay', 'shiprocket'];

        foreach ($skipPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function isLocalIp(?string $ip): bool
    {
        if (!$ip) {
            return true;
        }

        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
