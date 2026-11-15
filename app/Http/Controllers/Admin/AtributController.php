<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helm;
use App\Models\Kategori;
use App\Models\Merk;
use App\Models\Warna;
use Illuminate\Http\Request;

class AtributController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'merk'); // 'merk', 'kategori', 'warna'
        $search = $request->filled('search') ? trim($request->search) : null;

        // Metrik Utama
        $totalMerk     = Merk::count();
        $totalKategori = Kategori::count();
        $totalWarna    = Warna::count();
        $totalProduk   = Helm::count();

        // 1. Data Merk
        $merkQuery = Merk::withCount('helms')->orderBy('nama_merk', 'asc');
        if ($search && $tab === 'merk') {
            $merkQuery->where('nama_merk', 'like', "%{$search}%");
        }
        $merks = $merkQuery->get();

        // 2. Data Kategori
        $kategoriQuery = Kategori::withCount('helms')->orderBy('nama_kategori', 'asc');
        if ($search && $tab === 'kategori') {
            $kategoriQuery->where('nama_kategori', 'like', "%{$search}%")
                          ->orWhere('deskripsi', 'like', "%{$search}%");
        }
        $kategoris = $kategoriQuery->get();

        // 3. Data Warna
        $warnaQuery = Warna::orderBy('nama_warna', 'asc');
        if ($search && $tab === 'warna') {
            $warnaQuery->where('nama_warna', 'like', "%{$search}%")
                       ->orWhere('jenis_finishing', 'like', "%{$search}%");
        }
        $warnas = $warnaQuery->get();

        return view('admin.atribut.index', compact(
            'tab',
            'search',
            'merks',
            'kategoris',
            'warnas',
            'totalMerk',
            'totalKategori',
            'totalWarna',
            'totalProduk'
        ));
    }
}
