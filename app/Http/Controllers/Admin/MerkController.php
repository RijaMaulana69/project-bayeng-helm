<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Merk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerkController extends Controller
{
    public function index()
    {
        $merks = Merk::orderBy('nama_merk', 'asc')->get();

        return view('admin.merk.index', compact('merks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_merk' => 'required|string|max:255',
        ]);

        $exists = Merk::whereRaw('LOWER(nama_merk) = ?', [strtolower($request->nama_merk)])->exists();

        if ($exists) {
            return redirect()->route('admin.merk.index')
                ->with('error', 'Merk sudah ada.');
        }

        $merk = Merk::create([
            'nama_merk' => $request->nama_merk,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('create_merk', 'Menambahkan merk: ' . $merk->nama_merk, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.merk.index')
            ->with('success', 'Merk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_merk' => 'required|string|max:255',
        ]);

        $merk = Merk::findOrFail($id);

        $exists = Merk::whereRaw('LOWER(nama_merk) = ?', [strtolower($request->nama_merk)])
            ->where('id_merk', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.merk.index')
                ->with('error', 'Merk sudah ada.');
        }

        $oldName = $merk->nama_merk;
        $merk->update([
            'nama_merk' => $request->nama_merk,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('update_merk', 'Mengubah merk: ' . $oldName . ' -> ' . $merk->nama_merk, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.merk.index')
            ->with('success', 'Merk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $merk = Merk::findOrFail($id);

        try {
            $nama = $merk->nama_merk;
            $merk->delete();

            $user = Auth::guard('web')->user();
            ActivityLog::log('delete_merk', 'Menghapus merk: ' . $nama, 'user', $user->id_user, $user->username);

            return redirect()->route('admin.merk.index')
                ->with('success', 'Merk berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.merk.index')
                ->with('error', 'Merk tidak dapat dihapus karena masih digunakan.');
        }
    }
}
