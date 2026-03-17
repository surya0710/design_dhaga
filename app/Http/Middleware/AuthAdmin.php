<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in → admin login
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Logged in but not admin
        if (Auth::user()->utype !== 'ADM') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'You are not allowed to access admin panel');
        }

        // Admin user → allow
        return $next($request);
    }

}
