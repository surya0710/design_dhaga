<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;
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

        // Fix localhost IP
        $ip = $request->ip() === '127.0.0.1' ? '8.8.8.8' : $request->ip();

        // Geo API
        $geo = Http::get("http://ip-api.com/json/{$ip}")->json();

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
}