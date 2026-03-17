<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Utype
{
    public function handle(Request $request, Closure $next, $type)
    {
        if (Auth::check() && Auth::user()->utype === $type) {
            return $next($request);
        }

        abort(403); // Unauthorized
    }
}