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
        $kategoris = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $exists = Kategori::whereRaw('LOWER(nama_kategori) = ?', [strtolower($request->nama_kategori)])->exists();

        if ($exists) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori sudah ada.');
        }

        $kategori = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('create_kategori', 'Menambahkan kategori: ' . $kategori->nama_kategori, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = Kategori::findOrFail($id);

        $exists = Kategori::whereRaw('LOWER(nama_kategori) = ?', [strtolower($request->nama_kategori)])
            ->where('id_kategori', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori sudah ada.');
        }

        $oldName = $kategori->nama_kategori;
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('update_kategori', 'Mengubah kategori: ' . $oldName . ' -> ' . $kategori->nama_kategori, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        try {
            $nama = $kategori->nama_kategori;
            $kategori->delete();

            $user = Auth::guard('web')->user();
            ActivityLog::log('delete_kategori', 'Menghapus kategori: ' . $nama, 'user', $user->id_user, $user->username);

            return redirect()->route('admin.kategori.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan.');
        }
    }
}
