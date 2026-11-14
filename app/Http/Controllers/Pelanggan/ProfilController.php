<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        return view('pelanggan.profil', compact('pelanggan'));
    }

    public function update(Request $request)
    {
        $pelanggan = Auth::guard('pelanggan')->user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'password' => 'nullable|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        $pelanggan->nama = $request->nama;
        $pelanggan->telepon = $request->telepon;

        if ($request->filled('password')) {
            $pelanggan->password = $request->password;
        }

        $pelanggan->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
