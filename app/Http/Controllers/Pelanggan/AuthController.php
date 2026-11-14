<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('pelanggan.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rate limiting: max 5 attempts per minute
        $throttleKey = 'pelanggan-login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            ActivityLog::log('login_blocked', "Login pelanggan diblokir (rate limit): {$request->email}", 'guest', null, $request->email);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->withInput($request->only('email'));
        }

        if (Auth::guard('pelanggan')->attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $pelanggan = Auth::guard('pelanggan')->user();
            ActivityLog::log('login_success', "Pelanggan login: {$pelanggan->nama}", 'pelanggan', $pelanggan->id_pelanggan, $pelanggan->nama);

            return redirect()->intended(route('katalog'))->with('success', 'Login berhasil! Selamat datang kembali.');
        }

        RateLimiter::hit($throttleKey, 60);
        ActivityLog::log('login_failed', "Login pelanggan gagal: {$request->email}", 'guest', null, $request->email);

        return back()->withErrors([
            'email' => 'Email atau password salah!',
        ])->withInput($request->only('email'));
    }

    public function showRegister()
    {
        return view('pelanggan.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:pelanggan,email|max:100',
            'telepon' => 'required|string|max:20',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'email.unique' => 'Email sudah terdaftar! Gunakan email lain.',
        ]);

        $pelanggan = Pelanggan::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => $request->password,
            'telepon' => $request->telepon,
        ]);

        Auth::guard('pelanggan')->login($pelanggan);
        $request->session()->regenerate();

        ActivityLog::log('register', "Pelanggan baru mendaftar: {$pelanggan->nama}", 'pelanggan', $pelanggan->id_pelanggan, $pelanggan->nama);

        return redirect()->route('katalog')->with('success', 'Registrasi berhasil! Selamat berbelanja.');
    }

    public function logout(Request $request)
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        if ($pelanggan) {
            ActivityLog::log('logout', "Pelanggan logout: {$pelanggan->nama}", 'pelanggan', $pelanggan->id_pelanggan, $pelanggan->nama);
        }

        Auth::guard('pelanggan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil logout.');
    }
}
