@extends('layouts.admin')

@section('title', 'Atribut & Taksonomi Produk')
@section('page-title', 'Atribut & Taksonomi Produk')

@section('content')
<div class="space-y-4" 
     x-data="{
        tab: '{{ $tab }}',
        deleteModal: false,
        deleteUrl: '',
        deleteName: '',
        deleteType: '',
        confirmDelete(url, name, type) {
            this.deleteUrl = url;
            this.deleteName = name;
            this.deleteType = type;
            this.deleteModal = true;
        },

        // Modal Merk
        merkModal: false,
        merkMode: 'add',
        merkId: '',
        merkNama: '',
        merkDeskripsi: '',
        merkActionUrl: '{{ route('admin.merk.store') }}',
        openAddMerk() {
            this.merkMode = 'add';
            this.merkId = '';
            this.merkNama = '';
            this.merkDeskripsi = '';
            this.merkActionUrl = '{{ route('admin.merk.store') }}';
            this.merkModal = true;
        },
        openEditMerk(m) {
            this.merkMode = 'edit';
            this.merkId = m.id_merk;
            this.merkNama = m.nama_merk;
            this.merkDeskripsi = m.deskripsi || '';
            this.merkActionUrl = '{{ url('admin/merk') }}/' + m.id_merk;
            this.merkModal = true;
        },

        // Modal Kategori
        kategoriModal: false,
        kategoriMode: 'add',
        kategoriId: '',
        kategoriNama: '',
        kategoriDeskripsi: '',
        kategoriIcon: 'fa-solid fa-helmet-safety',
        kategoriActionUrl: '{{ route('admin.kategori.store') }}',
        openAddKategori() {
            this.kategoriMode = 'add';
            this.kategoriId = '';
            this.kategoriNama = '';
            this.kategoriDeskripsi = '';
            this.kategoriIcon = 'fa-solid fa-helmet-safety';
            this.kategoriActionUrl = '{{ route('admin.kategori.store') }}';
            this.kategoriModal = true;
        },
        openEditKategori(k) {
            this.kategoriMode = 'edit';
            this.kategoriId = k.id_kategori;
            this.kategoriNama = k.nama_kategori;
            this.kategoriDeskripsi = k.deskripsi || '';
            this.kategoriIcon = k.icon || 'fa-solid fa-helmet-safety';
            this.kategoriActionUrl = '{{ url('admin/kategori') }}/' + k.id_kategori;
            this.kategoriModal = true;
        },

        // Modal Warna
        warnaModal: false,
        warnaMode: 'add',
        warnaId: '',
        warnaNama: '',
        warnaHex: '#1E293B',
        warnaFinishing: 'glossy',
        warnaActionUrl: '{{ route('admin.warna.store') }}',
        openAddWarna() {
            this.warnaMode = 'add';
            this.warnaId = '';
            this.warnaNama = '';
            this.warnaHex = '#1E293B';
            this.warnaFinishing = 'glossy';
            this.warnaActionUrl = '{{ route('admin.warna.store') }}';
            this.warnaModal = true;
        },
        openEditWarna(w) {
            this.warnaMode = 'edit';
            this.warnaId = w.id_warna;
            this.warnaNama = w.nama_warna;
            this.warnaHex = w.kode_hex || '#1E293B';
            this.warnaFinishing = w.jenis_finishing || 'glossy';
            this.warnaActionUrl = '{{ url('admin/warna') }}/' + w.id_warna;
            this.warnaModal = true;
        }
     }">

    {{-- 1. Header Section: Title & Unified Add Action --}}
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Atribut Helm
        </h1>

        <div>
            {{-- Button Tambah berdasarkan Tab Aktif --}}
            <button type="button" 
                    x-show="tab === 'merk'" 
                    @click="openAddMerk()"
                    class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span>Tambah Merk</span>
            </button>

            <button type="button" 
                    x-show="tab === 'kategori'" 
                    @click="openAddKategori()"
                    class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span>Tambah Kategori</span>
            </button>

            <button type="button" 
                    x-show="tab === 'warna'" 
                    @click="openAddWarna()"
                    class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span>Tambah Warna</span>
            </button>
        </div>
    </div>

    {{-- 2. Stat Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a href="{{ route('admin.atribut.index', ['tab' => 'merk']) }}" 
           wire:navigate
           class="p-4 rounded-xl border transition-all {{ $tab === 'merk' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-800 border-slate-200/90 hover:border-slate-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider {{ $tab === 'merk' ? 'text-slate-300' : 'text-slate-400' }}">
                    Merk & Brand Resmi
                </span>
                <i class="fa-solid fa-tag text-xs {{ $tab === 'merk' ? 'text-slate-300' : 'text-slate-400' }}"></i>
            </div>
            <span class="text-xl sm:text-2xl font-bold mt-1.5 block {{ $tab === 'merk' ? 'text-white' : 'text-slate-900' }}">
                {{ $totalMerk }} Merk
            </span>
            <span class="text-[11px] mt-0.5 block {{ $tab === 'merk' ? 'text-slate-300' : 'text-slate-500' }}">
                Menyediakan {{ $totalProduk }} model helm aktif
            </span>
        </a>

        <a href="{{ route('admin.atribut.index', ['tab' => 'kategori']) }}" 
           wire:navigate
           class="p-4 rounded-xl border transition-all {{ $tab === 'kategori' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-800 border-slate-200/90 hover:border-slate-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider {{ $tab === 'kategori' ? 'text-slate-300' : 'text-slate-400' }}">
                    Kategori & Tipe Berkendara
                </span>
                <i class="fa-solid fa-layer-group text-xs {{ $tab === 'kategori' ? 'text-slate-300' : 'text-slate-400' }}"></i>
            </div>
            <span class="text-xl sm:text-2xl font-bold mt-1.5 block {{ $tab === 'kategori' ? 'text-white' : 'text-slate-900' }}">
                {{ $totalKategori }} Tipe Helm
            </span>
            <span class="text-[11px] mt-0.5 block {{ $tab === 'kategori' ? 'text-slate-300' : 'text-slate-500' }}">
                Full face, half face, retro, cross
            </span>
        </a>

        <a href="{{ route('admin.atribut.index', ['tab' => 'warna']) }}" 
           wire:navigate
           class="p-4 rounded-xl border transition-all {{ $tab === 'warna' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-800 border-slate-200/90 hover:border-slate-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider {{ $tab === 'warna' ? 'text-slate-300' : 'text-slate-400' }}">
                    Palet Warna Swatch
                </span>
                <i class="fa-solid fa-palette text-xs {{ $tab === 'warna' ? 'text-slate-300' : 'text-slate-400' }}"></i>
            </div>
            <span class="text-xl sm:text-2xl font-bold mt-1.5 block {{ $tab === 'warna' ? 'text-white' : 'text-slate-900' }}">
                {{ $totalWarna }} Pilihan Warna
            </span>
            <span class="text-[11px] mt-0.5 block {{ $tab === 'warna' ? 'text-slate-300' : 'text-slate-500' }}">
                Dengan kode HEX & jenis cat fisik
            </span>
        </a>
    </div>

    {{-- 3. Toolbar: Tab Switcher & Search Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pt-1">
        {{-- Tab Pills --}}
        <div class="flex items-center gap-1.5 border-b sm:border-b-0 border-slate-200 pb-2 sm:pb-0">
            <a href="{{ route('admin.atribut.index', ['tab' => 'merk']) }}" 
               wire:navigate
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1.5 {{ $tab === 'merk' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                <i class="fa-solid fa-tag text-[10px]"></i>
                <span>Merk ({{ $totalMerk }})</span>
            </a>

            <a href="{{ route('admin.atribut.index', ['tab' => 'kategori']) }}" 
               wire:navigate
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1.5 {{ $tab === 'kategori' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                <i class="fa-solid fa-layer-group text-[10px]"></i>
                <span>Kategori ({{ $totalKategori }})</span>
            </a>

            <a href="{{ route('admin.atribut.index', ['tab' => 'warna']) }}" 
               wire:navigate
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1.5 {{ $tab === 'warna' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                <i class="fa-solid fa-palette text-[10px]"></i>
                <span>Warna Swatch ({{ $totalWarna }})</span>
            </a>
        </div>

        {{-- Search Input --}}
        <form method="GET" action="{{ route('admin.atribut.index') }}" class="relative min-w-[220px]">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input type="text" 
                   name="search" 
                   value="{{ $search }}" 
                   placeholder="Cari {{ $tab }}..." 
                   class="w-full h-8 pl-9 pr-7 rounded-lg bg-white border border-slate-200/90 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
            @if($search)
                <a href="{{ route('admin.atribut.index', ['tab' => $tab]) }}" 
                   wire:navigate
                   class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- ================= TAB 1: MERK & BRAND ================= --}}
    @if ($tab === 'merk')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse ($merks as $m)
                <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs flex items-center justify-between gap-3 hover:border-slate-300 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Brand Logo / Initial --}}
                        <div class="w-11 h-11 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                            @if($m->logo_url)
                                <img src="{{ $m->logo_url }}" alt="{{ $m->nama_merk }}" class="w-full h-full object-contain p-1">
                            @else
                                <span class="font-bold text-slate-700 text-xs tracking-wider">
                                    {{ strtoupper(substr($m->nama_merk, 0, 3)) }}
                                </span>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 text-xs truncate">
                                {{ $m->nama_merk }}
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                {{ $m->deskripsi ?: 'Merk resmi helm SNI/DOT' }}
                            </p>
                            <span class="inline-block mt-1 text-[10px] font-semibold text-blue-600">
                                {{ $m->helms_count }} Model Helm
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" 
                                @click="openEditMerk({{ Js::from($m) }})"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                                title="Edit Merk">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                        </button>
                        <button type="button" 
                                @click="confirmDelete('{{ route('admin.merk.destroy', $m->id_merk) }}', '{{ addslashes($m->nama_merk) }}', 'Merk')"
                                class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                title="Hapus Merk">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl border border-slate-200/90 p-8 text-center text-xs text-slate-500">
                    Tidak ada merk ditemukan.
                </div>
            @endforelse
        </div>

    {{-- ================= TAB 2: KATEGORI & TIPE HELM ================= --}}
    @elseif ($tab === 'kategori')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-3">
            @forelse ($kategoris as $k)
                <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs flex items-start justify-between gap-3 hover:border-slate-300 transition-colors">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-lg bg-slate-900 text-white shrink-0 flex items-center justify-center text-sm shadow-2xs">
                            <i class="{{ $k->icon ?: 'fa-solid fa-helmet-safety' }}"></i>
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-900 text-xs">
                                    {{ $k->nama_kategori }}
                                </h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $k->helms_count }} Model
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                {{ $k->deskripsi ?: 'Klasifikasi jenis helm dan peruntukan berkendara' }}
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" 
                                @click="openEditKategori({{ Js::from($k) }})"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                                title="Edit Kategori">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                        </button>
                        <button type="button" 
                                @click="confirmDelete('{{ route('admin.kategori.destroy', $k->id_kategori) }}', '{{ addslashes($k->nama_kategori) }}', 'Kategori')"
                                class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                title="Hapus Kategori">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl border border-slate-200/90 p-8 text-center text-xs text-slate-500">
                    Tidak ada kategori ditemukan.
                </div>
            @endforelse
        </div>

    {{-- ================= TAB 3: WARNA & FINISHING SWATCH ================= --}}
    @elseif ($tab === 'warna')
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse ($warnas as $w)
                @php
                    $finishingLabel = match($w->jenis_finishing) {
                        'doff' => 'Doff / Matte',
                        'metallic' => 'Metallic',
                        'carbon' => 'Carbon Fiber',
                        'graphic' => 'Grafis / Livery',
                        default => 'Glossy',
                    };
                    $finishingBadge = match($w->jenis_finishing) {
                        'doff' => 'bg-slate-100 text-slate-700 border-slate-200',
                        'metallic' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'carbon' => 'bg-zinc-800 text-zinc-200 border-zinc-700',
                        'graphic' => 'bg-purple-50 text-purple-700 border-purple-200',
                        default => 'bg-blue-50 text-blue-700 border-blue-100',
                    };
                @endphp
                <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-2xs flex items-center justify-between gap-3 hover:border-slate-300 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Swatch Color Circle --}}
                        <div class="w-8 h-8 rounded-full border border-slate-300 shadow-2xs shrink-0" 
                             style="background-color: {{ $w->kode_hex ?: '#1e293b' }};">
                        </div>

                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 text-xs truncate">
                                {{ $w->nama_warna }}
                            </h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="font-mono text-[10px] text-slate-500 font-semibold uppercase">
                                    {{ $w->kode_hex ?: '#1E293B' }}
                                </span>
                                <span class="text-slate-300">·</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold border {{ $finishingBadge }}">
                                    {{ $finishingLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" 
                                @click="openEditWarna({{ Js::from($w) }})"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                                title="Edit Warna">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                        </button>
                        <button type="button" 
                                @click="confirmDelete('{{ route('admin.warna.destroy', $w->id_warna) }}', '{{ addslashes($w->nama_warna) }}', 'Warna')"
                                class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                title="Hapus Warna">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl border border-slate-200/90 p-8 text-center text-xs text-slate-500">
                    Tidak ada palet warna ditemukan.
                </div>
            @endforelse
        </div>
    @endif

    {{-- ================= MODAL TAMBAH / EDIT MERK ================= --}}
    <div x-show="merkModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
         @keydown.escape.window="merkModal = false">
        <div class="bg-white w-full max-w-md rounded-2xl border border-slate-200 shadow-xl overflow-hidden" 
             @click.outside="merkModal = false">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm" x-text="merkMode === 'add' ? 'Tambah Merk Baru' : 'Edit Merk Helm'"></h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Daftarkan brand helm resmi untuk katalog toko</p>
                </div>
                <button type="button" @click="merkModal = false" class="text-slate-400 hover:text-slate-600 text-xs p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form :action="merkActionUrl" method="POST" enctype="multipart/form-data" class="p-4 space-y-3.5 text-xs">
                @csrf
                <template x-if="merkMode === 'edit'">
                    @method('PUT')
                </template>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Nama Merk Helm <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_merk" 
                           x-model="merkNama" 
                           required 
                           placeholder="Contoh: KYT, NHK, INK, AGV" 
                           class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Keterangan Singkat
                    </label>
                    <input type="text" 
                           name="deskripsi" 
                           x-model="merkDeskripsi" 
                           placeholder="Contoh: Brand helm balap terkemuka lisensi Italia" 
                           class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Logo Merk (PNG Transparan / SVG)
                    </label>
                    <input type="file" 
                           name="logo" 
                           accept="image/png,image/svg+xml,image/jpeg,image/webp" 
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-slate-900 file:text-white hover:file:bg-slate-800 file:cursor-pointer cursor-pointer border border-slate-200 rounded-lg p-1.5">
                    <p class="text-[10px] text-slate-400 mt-1">Disarankan format PNG transparan untuk tampilan logo brand terbaik.</p>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="merkModal = false" class="h-8 px-3 rounded-lg border border-slate-200 text-slate-700 font-medium">Batal</button>
                    <button type="submit" class="h-8 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium">Simpan Merk</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH / EDIT KATEGORI ================= --}}
    <div x-show="kategoriModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
         @keydown.escape.window="kategoriModal = false">
        <div class="bg-white w-full max-w-md rounded-2xl border border-slate-200 shadow-xl overflow-hidden" 
             @click.outside="kategoriModal = false">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm" x-text="kategoriMode === 'add' ? 'Tambah Kategori Baru' : 'Edit Kategori Helm'"></h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Klasifikasi tipe helm dan peruntukan berkendara</p>
                </div>
                <button type="button" @click="kategoriModal = false" class="text-slate-400 hover:text-slate-600 text-xs p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form :action="kategoriActionUrl" method="POST" class="p-4 space-y-3.5 text-xs">
                @csrf
                <template x-if="kategoriMode === 'edit'">
                    @method('PUT')
                </template>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Nama Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_kategori" 
                           x-model="kategoriNama" 
                           required 
                           placeholder="Contoh: Modular / Flip-Up" 
                           class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Deskripsi Peruntukan Helm
                    </label>
                    <textarea name="deskripsi" 
                              x-model="kategoriDeskripsi" 
                              rows="2" 
                              placeholder="Keterangan fungsi helm, perlindungan, dan kenyamanan..." 
                              class="w-full p-2.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"></textarea>
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Ikon FontAwesome
                    </label>
                    <input type="text" 
                           name="icon" 
                           x-model="kategoriIcon" 
                           placeholder="fa-solid fa-helmet-safety" 
                           class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="kategoriModal = false" class="h-8 px-3 rounded-lg border border-slate-200 text-slate-700 font-medium">Batal</button>
                    <button type="submit" class="h-8 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH / EDIT WARNA ================= --}}
    <div x-show="warnaModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
         @keydown.escape.window="warnaModal = false">
        <div class="bg-white w-full max-w-md rounded-2xl border border-slate-200 shadow-xl overflow-hidden" 
             @click.outside="warnaModal = false">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm" x-text="warnaMode === 'add' ? 'Tambah Warna Swatch Baru' : 'Edit Palet Warna'"></h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Palet warna visual untuk pilihan varian di etalase</p>
                </div>
                <button type="button" @click="warnaModal = false" class="text-slate-400 hover:text-slate-600 text-xs p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form :action="warnaActionUrl" method="POST" class="p-4 space-y-3.5 text-xs">
                @csrf
                <template x-if="warnaMode === 'edit'">
                    @method('PUT')
                </template>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Nama Warna <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_warna" 
                           x-model="warnaNama" 
                           required 
                           placeholder="Contoh: Solid Black, Nardo Grey" 
                           class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900">
                </div>

                {{-- Color Picker & Hex Input --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Kode Warna HEX <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="color" 
                               x-model="warnaHex" 
                               class="w-10 h-9 p-0.5 rounded-lg border border-slate-200 bg-white cursor-pointer shrink-0">
                        <input type="text" 
                               name="kode_hex" 
                               x-model="warnaHex" 
                               required 
                               placeholder="#1E293B" 
                               class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-mono font-bold text-slate-900 uppercase focus:outline-none focus:ring-1 focus:ring-slate-900">
                    </div>
                </div>

                {{-- Finishing Cat --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Jenis Finishing Cat <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis_finishing" 
                            x-model="warnaFinishing" 
                            required 
                            class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                        <option value="glossy">Glossy (Permukaan Mengkilap)</option>
                        <option value="doff">Doff / Matte (Tanpa Pantulan Cahaya)</option>
                        <option value="metallic">Metallic (Kilap Berbutir Metal)</option>
                        <option value="carbon">Carbon Fiber (Corak Serat Karbon)</option>
                        <option value="graphic">Grafis / Livery Racing (Motif Corak)</option>
                    </select>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="warnaModal = false" class="h-8 px-3 rounded-lg border border-slate-200 text-slate-700 font-medium">Batal</button>
                    <button type="submit" class="h-8 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium">Simpan Warna</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL KONFIRMASI HAPUS ================= --}}
    <div x-show="deleteModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
         @keydown.escape.window="deleteModal = false">
        
        <div class="bg-white w-full max-w-sm rounded-2xl border border-slate-200 shadow-xl overflow-hidden p-5 space-y-4"
             @click.outside="deleteModal = false">
            
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg mx-auto">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="text-center space-y-1">
                <h3 class="font-bold text-slate-900 text-sm">Hapus <span x-text="deleteType"></span>?</h3>
                <p class="text-xs text-slate-500">
                    Data <span class="font-semibold text-slate-800" x-text="deleteName"></span> akan dihapus permanen. Aksi ini tidak dapat dibatalkan.
                </p>
            </div>

            <form :action="deleteUrl" method="POST" class="flex items-center gap-2 pt-1">
                @csrf
                @method('DELETE')
                <button type="button" 
                        @click="deleteModal = false"
                        class="flex-1 h-9 rounded-lg border border-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 h-9 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-2xs cursor-pointer">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
