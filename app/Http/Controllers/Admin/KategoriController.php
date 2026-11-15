<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.atribut.index', ['tab' => 'kategori']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'deskripsi'     => 'nullable|string|max:255',
            'icon'          => 'nullable|string|max:50',
        ]);

        $exists = Kategori::whereRaw('LOWER(nama_kategori) = ?', [strtolower(trim($request->nama_kategori))])->exists();

        if ($exists) {
            return redirect()->route('admin.atribut.index', ['tab' => 'kategori'])
                ->with('error', 'Kategori dengan nama tersebut sudah ada.');
        }

        $kategori = Kategori::create([
            'nama_kategori' => trim($request->nama_kategori),
            'deskripsi'     => $request->deskripsi,
            'icon'          => $request->icon ?: 'fa-solid fa-helmet-safety',
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('create_kategori', 'Menambahkan kategori: ' . $kategori->nama_kategori, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.atribut.index', ['tab' => 'kategori'])
            ->with('success', "Kategori {$kategori->nama_kategori} berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'deskripsi'     => 'nullable|string|max:255',
            'icon'          => 'nullable|string|max:50',
        ]);

        $kategori = Kategori::findOrFail($id);

        $exists = Kategori::whereRaw('LOWER(nama_kategori) = ?', [strtolower(trim($request->nama_kategori))])
            ->where('id_kategori', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.atribut.index', ['tab' => 'kategori'])
                ->with('error', 'Nama kategori tersebut sudah digunakan.');
        }

        $oldName = $kategori->nama_kategori;
        $kategori->update([
            'nama_kategori' => trim($request->nama_kategori),
            'deskripsi'     => $request->deskripsi,
            'icon'          => $request->icon ?: 'fa-solid fa-helmet-safety',
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('update_kategori', "Mengubah kategori: {$oldName} -> {$kategori->nama_kategori}", 'user', $user->id_user, $user->username);

        return redirect()->route('admin.atribut.index', ['tab' => 'kategori'])
            ->with('success', "Kategori {$kategori->nama_kategori} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->helms()->count() > 0) {
            return redirect()->route('admin.atribut.index', ['tab' => 'kategori'])
                ->with('error', "Kategori {$kategori->nama_kategori} tidak dapat dihapus karena masih digunakan oleh {$kategori->helms()->count()} produk helm.");
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        $user = Auth::guard('web')->user();
        ActivityLog::log('delete_kategori', 'Menghapus kategori: ' . $nama, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.atribut.index', ['tab' => 'kategori'])
            ->with('success', "Kategori {$nama} berhasil dihapus.");
    }
}
