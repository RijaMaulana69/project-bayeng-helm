<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanController extends Controller
{
    public function index()
    {
        $toko = Toko::first();

        return view('admin.pengaturan.index', [
            'toko'       => $toko,
            'pengaturan' => $toko,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'alamat'    => 'required|string|max:500',
            'telepon'   => 'required|string|max:20',
            'logo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $toko = Toko::first();

        $data = [
            'nama_toko' => $request->nama_toko,
            'alamat'    => $request->alamat,
            'telepon'   => $request->telepon,
        ];

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($toko && $toko->logo && file_exists(public_path($toko->logo))) {
                unlink(public_path($toko->logo));
            }

            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/toko'), $logoName);
            $data['logo'] = 'uploads/toko/' . $logoName;
        }

        if ($toko) {
            $toko->update($data);
        } else {
            $toko = Toko::create($data);
        }

        $user = Auth::guard('web')->user();
        ActivityLog::log('update_pengaturan', 'Mengubah pengaturan toko: ' . $toko->nama_toko, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.pengaturan.index')
            ->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
