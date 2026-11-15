<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\VarianHelm;
use App\Models\Warna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarnaController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.atribut.index', ['tab' => 'warna']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_warna'      => 'required|string|max:100',
            'kode_hex'        => 'required|string|max:10',
            'jenis_finishing' => 'required|in:glossy,doff,metallic,carbon,graphic',
        ]);

        $exists = Warna::whereRaw('LOWER(nama_warna) = ?', [strtolower(trim($request->nama_warna))])->exists();

        if ($exists) {
            return redirect()->route('admin.atribut.index', ['tab' => 'warna'])
                ->with('error', 'Warna dengan nama tersebut sudah ada.');
        }

        $hex = strtoupper(trim($request->kode_hex));
        if (!str_starts_with($hex, '#')) {
            $hex = '#' . $hex;
        }

        $warna = Warna::create([
            'nama_warna'      => trim($request->nama_warna),
            'kode_hex'        => $hex,
            'jenis_finishing' => $request->jenis_finishing,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('create_warna', 'Menambahkan palet warna: ' . $warna->nama_warna, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.atribut.index', ['tab' => 'warna'])
            ->with('success', "Warna {$warna->nama_warna} berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_warna'      => 'required|string|max:100',
            'kode_hex'        => 'required|string|max:10',
            'jenis_finishing' => 'required|in:glossy,doff,metallic,carbon,graphic',
        ]);

        $warna = Warna::findOrFail($id);

        $exists = Warna::whereRaw('LOWER(nama_warna) = ?', [strtolower(trim($request->nama_warna))])
            ->where('id_warna', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.atribut.index', ['tab' => 'warna'])
                ->with('error', 'Nama warna tersebut sudah digunakan.');
        }

        $hex = strtoupper(trim($request->kode_hex));
        if (!str_starts_with($hex, '#')) {
            $hex = '#' . $hex;
        }

        $oldName = $warna->nama_warna;
        $newName = trim($request->nama_warna);

        // Jika nama warna berubah, sinkronkan juga tabel varian_helm yang mereferensikannya
        if ($oldName !== $newName) {
            VarianHelm::where('warna', $oldName)->update(['warna' => $newName]);
        }

        $warna->update([
            'nama_warna'      => $newName,
            'kode_hex'        => $hex,
            'jenis_finishing' => $request->jenis_finishing,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('update_warna', "Mengubah warna: {$oldName} -> {$newName}", 'user', $user->id_user, $user->username);

        return redirect()->route('admin.atribut.index', ['tab' => 'warna'])
            ->with('success', "Warna {$newName} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $warna = Warna::findOrFail($id);

        $usedCount = VarianHelm::where('warna', $warna->nama_warna)->count();
        if ($usedCount > 0) {
            return redirect()->route('admin.atribut.index', ['tab' => 'warna'])
                ->with('error', "Warna {$warna->nama_warna} tidak dapat dihapus karena masih digunakan pada {$usedCount} varian helm aktif.");
        }

        $nama = $warna->nama_warna;
        $warna->delete();

        $user = Auth::guard('web')->user();
        ActivityLog::log('delete_warna', 'Menghapus warna: ' . $nama, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.atribut.index', ['tab' => 'warna'])
            ->with('success', "Warna {$nama} berhasil dihapus.");
    }
}
