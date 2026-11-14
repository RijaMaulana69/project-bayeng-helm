<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        // Rate limiting: max 5 attempts per minute
        $throttleKey = 'admin-login:' . Str::lower($request->username) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            ActivityLog::log('login_blocked', "Login admin diblokir (rate limit): {$request->username}", 'guest', null, $request->username);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->withInput($request->only('username'));
        }

        if (Auth::guard('web')->attempt(['username' => $request->username, 'password' => $request->password])) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::guard('web')->user();
            $user->update(['last_active' => now()]);

            ActivityLog::log('login_success', "Admin login: {$user->nama_lengkap} ({$user->role->nama_role})", 'user', $user->id_user, $user->username);

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Login berhasil! Selamat datang.');
        }

        RateLimiter::hit($throttleKey, 60);
        ActivityLog::log('login_failed', "Login admin gagal: {$request->username}", 'guest', null, $request->username);

        return back()->withErrors([
            'username' => 'Username atau password salah!',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            ActivityLog::log('logout', "Admin logout: {$user->nama_lengkap}", 'user', $user->id_user, $user->username);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil logout.');
    }
}
