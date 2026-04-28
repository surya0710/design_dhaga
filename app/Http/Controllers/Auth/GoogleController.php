<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect()
    {
        // Store the page user came from
        session(['url.intended' => url()->previous()]);
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser     = Socialite::driver('google')->stateless()->user();
            $intendedUrl    = session('url.intended', '/');
            session()->forget('url.intended');

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name'              => $googleUser->getName() ?? 'User',
                    'email'             => $googleUser->getEmail(),
                    'password'          => bcrypt(Str::random(16)),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'utype'             => 'USR',
                ]);
            } else {
                $user->update([
                    'google_id' => $user->google_id ?? $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            }

            Auth::login($user, true);

            // Redirect back to previous page
            return redirect()->to($intendedUrl);

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Google login failed');
        }
    }
}