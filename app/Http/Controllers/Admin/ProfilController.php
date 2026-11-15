<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();

        return view('admin.profil.index', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'password'      => [
                'nullable',
                'confirmed',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.regex'     => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        $user = Auth::guard('web')->user();

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::log('update_profil', 'Mengubah profil: ' . $user->nama_lengkap, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
