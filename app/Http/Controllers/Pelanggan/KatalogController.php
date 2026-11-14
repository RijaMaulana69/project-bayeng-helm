<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Helm;
use App\Models\Merk;
use App\Models\Kategori;

class KatalogController extends Controller
{
    public function index()
    {
        $merks = Merk::orderBy('nama_merk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('pelanggan.katalog.index', compact('merks', 'kategoris'));
    }

    public function show(Helm $helm)
    {
        $helm->load(['merk', 'kategori', 'varians']);
        $related = Helm::where('id_kategori', $helm->id_kategori)
            ->where('id_helm', '!=', $helm->id_helm)
            ->where('stok', '>', 0)
            ->take(6)
            ->get();

        return view('pelanggan.katalog.detail', compact('helm', 'related'));
    }
}
