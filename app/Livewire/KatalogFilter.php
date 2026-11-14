<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Helm;
use App\Models\Merk;
use App\Models\Kategori;

class KatalogFilter extends Component
{
    use WithPagination;

    public $search = '';
    public $merk_filter = '';
    public $kategori_filter = '';
    public $sort = 'terbaru';
    public $price_min = '';
    public $price_max = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'merk_filter' => ['except' => ''],
        'kategori_filter' => ['except' => ''],
        'sort' => ['except' => 'terbaru'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMerkFilter()
    {
        $this->resetPage();
    }

    public function updatingKategoriFilter()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'merk_filter', 'kategori_filter', 'sort', 'price_min', 'price_max']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Helm::with(['merk', 'kategori'])->where('stok', '>', 0);

        if ($this->search) {
            $query->where('nama_helm', 'like', "%{$this->search}%");
        }
        if ($this->merk_filter) {
            $query->where('id_merk', $this->merk_filter);
        }
        if ($this->kategori_filter) {
            $query->where('id_kategori', $this->kategori_filter);
        }
        if ($this->price_min) {
            $query->where('harga_jual', '>=', $this->price_min);
        }
        if ($this->price_max) {
            $query->where('harga_jual', '<=', $this->price_max);
        }

        $query = match ($this->sort) {
            'termurah' => $query->orderBy('harga_jual', 'asc'),
            'termahal' => $query->orderBy('harga_jual', 'desc'),
            'nama' => $query->orderBy('nama_helm', 'asc'),
            default => $query->orderBy('id_helm', 'desc'),
        };

        return view('livewire.katalog-filter', [
            'helms' => $query->paginate(12),
            'merks' => Merk::orderBy('nama_merk')->get(),
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }
}
