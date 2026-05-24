<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Visitor;

class TrackUser
{
    public function handle(Request $request, Closure $next)
    {
        $agent = new Agent();

        // Visitor ID
        $visitorId = $request->cookie('visitor_id');

        if (!$visitorId) {
            $visitorId = (string) Str::uuid();
            cookie()->queue(cookie('visitor_id', $visitorId, 60 * 24 * 365));
        }

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

        // Save or update visitor
        Visitor::updateOrCreate(
            ['visitor_id' => $visitorId],
            [
                'user_id' => auth()->id(),
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'referrer' => $request->headers->get('referer'),
                'utm_source' => $request->query('utm_source'),
                'utm_medium' => $request->query('utm_medium'),
                'utm_campaign' => $request->query('utm_campaign'),
                'country' => $geo['country'] ?? null,
                'state' => $geo['regionName'] ?? null,
                'city' => $geo['city'] ?? null,
                'url' => $request->fullUrl(),
                'visited_at' => now(),
            ]
        );

        return $next($request);
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
