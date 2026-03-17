<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Find or create the user
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'email_verified_at' => now(),
                'password' => bcrypt(uniqid()), // random password
            ]
        );

        Auth::login($user);

        return redirect('/my-account');
    }

    public function authenticate(Request $request) {
        $idToken = $request->input('credential');

        $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
            $email = $payload['email'];
            $name = $payload['name'];

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'email_verified_at' => now(),
                    'password' => bcrypt(uniqid()),
                ]
            );

            Auth::login($user);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 401);
    }
}
