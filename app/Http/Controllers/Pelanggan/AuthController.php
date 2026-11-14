<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // 1. Validasi input dasar
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|max:64',
        ], [
            'email.required' => 'Alamat email atau No. WhatsApp wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.max' => 'Kata sandi melebihi batas maksimal 64 karakter.',
        ]);

        $loginInput = trim($request->input('email'));
        $password = $request->input('password');

        // Deteksi apakah input berupa Email atau Nomor Telepon
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        if ($isEmail) {
            $normalizedEmail = strtolower($loginInput);
            $credentials = [
                'email' => $normalizedEmail,
                'password' => $password,
            ];
            $throttleIdentifier = $normalizedEmail;
        } else {
            // Normalisasi nomor telepon: hapus karakter non-angka, standarkan awalan 08
            $cleanPhone = preg_replace('/[^0-9]/', '', $loginInput);
            if (str_starts_with($cleanPhone, '62')) {
                $cleanPhone = '0' . substr($cleanPhone, 2);
            } elseif (str_starts_with($cleanPhone, '8')) {
                $cleanPhone = '0' . $cleanPhone;
            }

            $credentials = [
                'telepon' => $cleanPhone,
                'password' => $password,
            ];
            $throttleIdentifier = $cleanPhone;
        }

        // Rate limiting: maksimal 5 percobaan gagal per 60 detik per akun + IP
        $throttleKey = 'pelanggan-login:' . Str::lower($throttleIdentifier) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            ActivityLog::log('login_blocked', "Login pelanggan diblokir (rate limit): {$throttleIdentifier}", 'guest', null, $throttleIdentifier);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam {$seconds} detik.",
            ])->withInput($request->only('email'));
        }

        $remember = $request->boolean('remember');

        if (Auth::guard('pelanggan')->attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $pelanggan = Auth::guard('pelanggan')->user();
            ActivityLog::log('login_success', "Pelanggan login: {$pelanggan->nama}", 'pelanggan', $pelanggan->id_pelanggan, $pelanggan->nama);

            return redirect()->intended(route('katalog'))->with('success', "Login berhasil! Selamat datang kembali, {$pelanggan->nama}.");
        }

        RateLimiter::hit($throttleKey, 60);
        ActivityLog::log('login_failed', "Login pelanggan gagal: {$throttleIdentifier}", 'guest', null, $throttleIdentifier);

        return back()->withErrors([
            'email' => 'Alamat email atau kata sandi yang Anda masukkan salah.',
        ])->withInput($request->only('email'));
    }

    public function showRegister()
    {
        return view('pelanggan.auth.register');
    }

    public function register(Request $request)
    {
        // 1. Pra-pembersihan & normalisasi input sebelum validasi
        $input = $request->all();

        if (isset($input['telepon'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $input['telepon']);
            if (str_starts_with($cleanPhone, '62')) {
                $cleanPhone = '0' . substr($cleanPhone, 2);
            } elseif (str_starts_with($cleanPhone, '8')) {
                $cleanPhone = '0' . $cleanPhone;
            }
            $input['telepon'] = $cleanPhone;
        }

        if (isset($input['email'])) {
            $input['email'] = strtolower(trim($input['email']));
        }

        if (isset($input['nama'])) {
            $input['nama'] = trim(preg_replace('/\s+/', ' ', $input['nama']));
        }

        $request->merge($input);

        // 2. Validasi ketat (nama maksimal 30 karakter, password minimal 8 & maksimal 64 karakter)
        $request->validate([
            'nama' => 'required|string|min:3|max:30',
            'email' => 'required|email|max:100|unique:pelanggan,email',
            'telepon' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:64',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.min' => 'Nama lengkap minimal 3 karakter.',
            'nama.max' => 'Nama lengkap maksimal 30 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan masuk.',
            'email.max' => 'Alamat email maksimal 100 karakter.',
            'telepon.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'telepon.min' => 'Nomor WhatsApp minimal 10 digit angka.',
            'telepon.max' => 'Nomor WhatsApp maksimal 15 digit angka.',
            'telepon.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.max' => 'Kata sandi maksimal 64 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.regex' => 'Kata sandi harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        // Format nama rapi (Title Case, contoh: "budi pratama" -> "Budi Pratama")
        $formattedNama = ucwords(strtolower($request->nama));

        $pelanggan = Pelanggan::create([
            'nama' => $formattedNama,
            'email' => $request->email,
            'password' => $request->password,
            'telepon' => $request->telepon,
        ]);

        Auth::guard('pelanggan')->login($pelanggan);
        $request->session()->regenerate();

        ActivityLog::log('register', "Pelanggan baru mendaftar: {$pelanggan->nama}", 'pelanggan', $pelanggan->id_pelanggan, $pelanggan->nama);

        return redirect()->intended(route('katalog'))->with('success', "Registrasi berhasil! Selamat datang di Bayeng Putra Helm, {$pelanggan->nama}.");
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
