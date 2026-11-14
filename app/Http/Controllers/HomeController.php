<?php

namespace App\Http\Controllers;

use App\Models\Helm;
use App\Models\Merk;
use App\Models\Kategori;
use App\Models\Toko;

class HomeController extends Controller
{
    public function index()
    {
        $toko = Toko::first();
        $produk_unggulan = Helm::with(['merk', 'kategori'])
            ->where('stok', '>', 0)
            ->orderBy('harga_jual', 'desc')
            ->take(8)
            ->get();
        $merks = Merk::orderBy('nama_merk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $total_produk = Helm::count();

        return view('home', compact('toko', 'produk_unggulan', 'merks', 'kategoris', 'total_produk'));
    }
}
