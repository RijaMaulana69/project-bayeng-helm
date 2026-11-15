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
        return redirect()->route('admin.atribut.index', ['tab' => 'merk']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_merk' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'logo'      => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $exists = Merk::whereRaw('LOWER(nama_merk) = ?', [strtolower(trim($request->nama_merk))])->exists();

        if ($exists) {
            return redirect()->route('admin.atribut.index', ['tab' => 'merk'])
                ->with('error', 'Merk dengan nama tersebut sudah ada.');
        }

        $logoName = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $logoName = 'merk_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/merk'), $logoName);
        }

        $merk = Merk::create([
            'nama_merk' => trim($request->nama_merk),
            'deskripsi' => $request->deskripsi,
            'logo'      => $logoName,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('create_merk', 'Menambahkan merk: ' . $merk->nama_merk, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.atribut.index', ['tab' => 'merk'])
            ->with('success', "Merk {$merk->nama_merk} berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_merk' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'logo'      => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $merk = Merk::findOrFail($id);

        $exists = Merk::whereRaw('LOWER(nama_merk) = ?', [strtolower(trim($request->nama_merk))])
            ->where('id_merk', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.atribut.index', ['tab' => 'merk'])
                ->with('error', 'Nama merk tersebut sudah digunakan.');
        }

        $logoName = $merk->logo;
        if ($request->hasFile('logo')) {
            if ($logoName && file_exists(public_path('uploads/merk/' . $logoName))) {
                @unlink(public_path('uploads/merk/' . $logoName));
            }
            $file = $request->file('logo');
            $logoName = 'merk_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/merk'), $logoName);
        }

        $oldName = $merk->nama_merk;
        $merk->update([
            'nama_merk' => trim($request->nama_merk),
            'deskripsi' => $request->deskripsi,
            'logo'      => $logoName,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('update_merk', "Mengubah merk: {$oldName} -> {$merk->nama_merk}", 'user', $user->id_user, $user->username);

        return redirect()->route('admin.atribut.index', ['tab' => 'merk'])
            ->with('success', "Merk {$merk->nama_merk} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $merk = Merk::findOrFail($id);

        if ($merk->helms()->count() > 0) {
            return redirect()->route('admin.atribut.index', ['tab' => 'merk'])
                ->with('error', "Merk {$merk->nama_merk} tidak dapat dihapus karena masih digunakan oleh {$merk->helms()->count()} produk helm.");
        }

        if ($merk->logo && file_exists(public_path('uploads/merk/' . $merk->logo))) {
            @unlink(public_path('uploads/merk/' . $merk->logo));
        }

        $nama = $merk->nama_merk;
        $merk->delete();

        $user = Auth::guard('web')->user();
        ActivityLog::log('delete_merk', 'Menghapus merk: ' . $nama, 'user', $user->id_user, $user->username);

        return redirect()->route('admin.atribut.index', ['tab' => 'merk'])
            ->with('success', "Merk {$nama} berhasil dihapus.");
    }
}
