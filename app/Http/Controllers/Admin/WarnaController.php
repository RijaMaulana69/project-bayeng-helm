<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Warna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarnaController extends Controller
{
    public function index()
    {
        $warnas = Warna::orderBy('nama_warna', 'asc')->get();

        return view('admin.warna.index', compact('warnas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_warna' => 'required|string|max:255',
        ]);

        $exists = Warna::whereRaw('LOWER(nama_warna) = ?', [strtolower($request->nama_warna)])->exists();

        if ($exists) {
            return redirect()->route('admin.warna.index')
                ->with('error', 'Warna sudah ada.');
        }

        $warna = Warna::create([
            'nama_warna' => $request->nama_warna,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('create_warna', 'Menambahkan warna: ' . $warna->nama_warna, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.warna.index')
            ->with('success', 'Warna berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $warna = Warna::findOrFail($id);

        try {
            $nama = $warna->nama_warna;
            $warna->delete();

            $user = Auth::guard('web')->user();
            ActivityLog::log('delete_warna', 'Menghapus warna: ' . $nama, 'user', $user->id_user, $user->username);

            return redirect()->route('admin.warna.index')
                ->with('success', 'Warna berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.warna.index')
                ->with('error', 'Warna tidak dapat dihapus karena masih digunakan.');
        }
    }
}
