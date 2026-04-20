<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use App\Models\Category;

class UserController extends Controller
{

    protected $categories;

    public function __construct()
    {
        $this->categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with('children')
            ->get();
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $redirectTo  = $request->input('redirect_to', route('home'));

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Adjust redirect logic to your needs
            $user = Auth::user();
            $redirect = $user->utype === 'ADM' ? route('admin.dashboard') : $redirectTo;

            return response()->json([
                'success'  => true,
                'redirect' => $redirect,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email or password is incorrect.',
        ], 401);
    }

    public function register()
    {
        $categories = $this->categories;
        return view('user.register', compact('categories'));
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|numeric|digits:10|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'This email belongs to a different account.',
            'phone.unique' => 'This mobile belongs to a different account.',
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'mobile' => $request->phone,
            'role' => 'USR',
            'password' => $request->password, // auto hashed
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('user.forgot-password');
        }

        $request->validate([ 'email' => ['required', 'email']]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('user.reset-password', [
                'email' => $request->query('email'),
                'token' => $request->route('token')
            ]);
        }

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('home')->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    function verificationNotice()
    {
        $categories = $this->categories;
        return view('auth.verify', compact('categories'));
    }

}
