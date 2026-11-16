@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')
@section('page-title', 'Tambah Produk Baru')

@section('content')
<div class="space-y-4 max-w-6xl mx-auto"
     x-data="{
        warnas: {{ Js::from(old('warna', [])) }},
        ukurans: {{ Js::from(old('ukuran', [])) }},
        warnaColors: {{ Js::from($warnas->pluck('kode_hex', 'nama_warna')) }},
        variantData: {{ Js::from(old('variant_stok', [])) }},
        hargaAwal: '{{ old('harga_awal', '') }}',
        hargaJual: '{{ old('harga_jual', '') }}',
        imagePreview: null,
        previewImage(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => { this.imagePreview = ev.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.imagePreview = null;
            }
        },
        clearImage() {
            this.imagePreview = null;
            const input = document.getElementById('gambar-input');
            if (input) input.value = '';
        },
        get margin() {
            const beli = Number(this.hargaAwal) || 0;
            const jual = Number(this.hargaJual) || 0;
            return Math.max(0, jual - beli);
        },
        get marginPercent() {
            const jual = Number(this.hargaJual) || 0;
            if (jual <= 0) return 0;
            return Math.round((this.margin / jual) * 100);
        },
        toggleWarna(nama) {
            const idx = this.warnas.indexOf(nama);
            if (idx > -1) {
                this.warnas.splice(idx, 1);
            } else {
                this.warnas.push(nama);
            }
        },
        toggleUkuran(u) {
            const idx = this.ukurans.indexOf(u);
            if (idx > -1) {
                this.ukurans.splice(idx, 1);
            } else {
                this.ukurans.push(u);
            }
        },
        getStok(warna, ukuran) {
            const key = warna + '|' + ukuran;
            return this.variantData[key] !== undefined ? parseInt(this.variantData[key]) || 0 : 0;
        },
        setStok(warna, ukuran, val) {
            const key = warna + '|' + ukuran;
            this.variantData[key] = Math.max(0, parseInt(val) || 0);
        },
        changeStok(warna, ukuran, delta) {
            const current = this.getStok(warna, ukuran);
            this.setStok(warna, ukuran, current + delta);
        },
        setAllStok(val) {
            this.warnas.forEach(w => {
                this.ukurans.forEach(u => {
                    this.setStok(w, u, val);
                });
            });
        },
        get totalUnitFisik() {
            let sum = 0;
            this.warnas.forEach(w => {
                this.ukurans.forEach(u => {
                    sum += this.getStok(w, u);
                });
            });
            return sum;
        },
        get totalKombinasi() {
            return this.warnas.length * this.ukurans.length;
        }
     }">

    {{-- 1. Header Minimalist --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.produk.index') }}" 
               wire:navigate
               class="text-xs text-slate-500 hover:text-slate-900 font-medium inline-flex items-center gap-1.5 transition-colors mb-1">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali ke Katalog</span>
            </a>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Tambah Produk Helm Baru
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Daftarkan model helm baru beserta varian warna, ukuran, dan stok fisik gerai
            </p>
        </div>
    </div>

    {{-- 2. Main Form Grid --}}
    <form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Kolom Kiri: Informasi Utama & Varian (lg:col-span-8) --}}
            <div class="lg:col-span-8 space-y-4">

                {{-- Blok 1: Informasi Produk --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
                        <span class="font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-slate-400 text-[11px]"></i>
                            <span>Informasi Produk</span>
                        </span>
                        <span class="text-[11px] text-slate-400">Data Wajib</span>
                    </div>

                    <div class="p-4 sm:p-5 space-y-3.5 text-xs">
                        {{-- Nama Helm --}}
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">
                                Nama Model Helm <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_helm" 
                                   value="{{ old('nama_helm') }}" 
                                   placeholder="Contoh: KYT TT Course Tourist Doff" 
                                   required 
                                   class="w-full h-9 px-3 rounded-lg bg-slate-50 border @error('nama_helm') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                            @error('nama_helm')
                                <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Merk & Kategori (2 Kolom) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Merk --}}
                            <div class="space-y-1">
                                <label class="block font-semibold text-slate-700">
                                    Merk Helm <span class="text-rose-500">*</span>
                                </label>
                                <select name="id_merk" 
                                        required 
                                        class="w-full h-9 px-2.5 rounded-lg bg-slate-50 border @error('id_merk') border-rose-300 @else border-slate-200 @enderror text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400 cursor-pointer">
                                    <option value="">-- Pilih Merk --</option>
                                    @foreach ($merks as $m)
                                        <option value="{{ $m->id_merk }}" {{ old('id_merk') == $m->id_merk ? 'selected' : '' }}>
                                            {{ $m->nama_merk }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_merk')
                                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Kategori --}}
                            <div class="space-y-1">
                                <label class="block font-semibold text-slate-700">
                                    Kategori Helm <span class="text-rose-500">*</span>
                                </label>
                                <select name="id_kategori" 
                                        required 
                                        class="w-full h-9 px-2.5 rounded-lg bg-slate-50 border @error('id_kategori') border-rose-300 @else border-slate-200 @enderror text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400 cursor-pointer">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategoris as $k)
                                        <option value="{{ $k->id_kategori }}" {{ old('id_kategori') == $k->id_kategori ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_kategori')
                                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Deskripsi Produk --}}
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">
                                Deskripsi & Spesifikasi
                            </label>
                            <textarea name="deskripsi" 
                                      rows="3" 
                                      placeholder="Keterangan bahan shell, sertifikasi SNI/DOT, tipe pengunci tali, fitur visor..." 
                                      class="w-full p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors leading-relaxed">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Blok 2: Varian Warna, Ukuran & Stepper Stok Fisik (Mobile-First Ledger) --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
                        <span class="font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-slate-400 text-[11px]"></i>
                            <span>Varian & Stok Fisik</span>
                        </span>
                        <span class="text-[11px] text-slate-500 font-medium" x-show="totalKombinasi > 0" x-cloak>
                            <span class="font-bold text-slate-900 tabular-nums" x-text="totalKombinasi"></span> kombinasi · <span class="font-bold text-emerald-600 tabular-nums" x-text="totalUnitFisik"></span> unit total
                        </span>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4 text-xs">
                        {{-- 1. Pilihan Warna --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block font-semibold text-slate-700">
                                    1. Pilihan Warna Helm
                                </label>
                                <span class="text-[11px] text-slate-400" x-text="warnas.length > 0 ? warnas.length + ' warna dipilih' : 'Pilih warna'"></span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($warnas as $w)
                                    <label class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border text-xs cursor-pointer select-none transition-all"
                                           :class="warnas.includes('{{ $w->nama_warna }}') ? 'bg-slate-900 text-white border-slate-900 font-medium shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                                        <input type="checkbox" 
                                               name="warna[]" 
                                               value="{{ $w->nama_warna }}" 
                                               class="sr-only"
                                               @change="toggleWarna('{{ $w->nama_warna }}')"
                                               :checked="warnas.includes('{{ $w->nama_warna }}')">
                                        <span class="w-2.5 h-2.5 rounded-full border border-black/10 shrink-0" style="background-color: {{ $w->kode_hex ?: '#334155' }};"></span>
                                        <span>{{ $w->nama_warna }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- 2. Pilihan Ukuran --}}
                        <div class="space-y-2 pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <label class="block font-semibold text-slate-700">
                                    2. Pilihan Ukuran Helm
                                </label>
                                <span class="text-[11px] text-slate-400" x-text="ukurans.length > 0 ? ukurans.length + ' ukuran dipilih' : 'Pilih ukuran'"></span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $u)
                                    <label class="inline-flex items-center justify-center w-11 h-8 rounded-lg border text-xs font-semibold cursor-pointer select-none transition-all tabular-nums"
                                           :class="ukurans.includes('{{ $u }}') ? 'bg-slate-900 text-white border-slate-900 shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                                        <input type="checkbox" 
                                               name="ukuran[]" 
                                               value="{{ $u }}" 
                                               class="sr-only"
                                               @change="toggleUkuran('{{ $u }}')"
                                               :checked="ukurans.includes('{{ $u }}')">
                                        <span>{{ $u }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- 3. Daftar Kartu Varian dengan Stepper Stok (Mobile-First Ledger) --}}
                        <div class="pt-3 border-t border-slate-100 space-y-3" x-show="warnas.length > 0 && ukurans.length > 0" x-cloak>
                            {{-- Toolbar Preset Cepat (Bulk Fill Presets) --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                                <div class="text-[11px] text-slate-600 font-medium">
                                    Set Cepat Semua Stok:
                                </div>
                                <div class="flex items-center gap-1 flex-wrap">
                                    <button type="button" 
                                            @click="setAllStok(0)" 
                                            class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 cursor-pointer active:scale-95 transition-transform shadow-2xs">
                                        Nol (0)
                                    </button>
                                    <button type="button" 
                                            @click="setAllStok(1)" 
                                            class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 cursor-pointer active:scale-95 transition-transform shadow-2xs">
                                        Semua 1
                                    </button>
                                    <button type="button" 
                                            @click="setAllStok(3)" 
                                            class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 cursor-pointer active:scale-95 transition-transform shadow-2xs">
                                        Semua 3
                                    </button>
                                    <button type="button" 
                                            @click="setAllStok(5)" 
                                            class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 cursor-pointer active:scale-95 transition-transform shadow-2xs">
                                        Semua 5
                                    </button>
                                    <button type="button" 
                                            @click="setAllStok(10)" 
                                            class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 cursor-pointer active:scale-95 transition-transform shadow-2xs">
                                        Semua 10
                                    </button>
                                </div>
                            </div>

                            {{-- Mobile-First Variant Ledger Cards --}}
                            <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden bg-white">
                                <template x-for="w in warnas" :key="w">
                                    <div>
                                        <template x-for="u in ukurans" :key="w + '|' + u">
                                            <div class="p-3 sm:p-3.5 hover:bg-slate-50/60 transition-colors flex items-center justify-between gap-3 border-b border-slate-100 last:border-b-0">
                                                {{-- Kolom Kiri: Info Kombinasi Warna & Ukuran --}}
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <span class="w-3.5 h-3.5 rounded-full border border-black/10 shrink-0 shadow-2xs" 
                                                          :style="'background-color: ' + (warnaColors[w] || '#334155')"></span>
                                                    <div class="min-w-0 truncate">
                                                        <div class="font-semibold text-xs text-slate-900 truncate flex items-center gap-1.5">
                                                            <span x-text="w"></span>
                                                            <span class="text-slate-300">·</span>
                                                            <span class="font-bold text-slate-700" x-text="'Size ' + u"></span>
                                                        </div>
                                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                                            <span x-show="getStok(w, u) > 0" class="text-emerald-600 font-medium" x-text="getStok(w, u) + ' unit siap jual'"></span>
                                                            <span x-show="getStok(w, u) === 0" class="text-slate-400">Stok belum diisi (0)</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Kolom Kanan: Stepper Kuantitas Touch-Friendly --}}
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <button type="button" 
                                                            @click="changeStok(w, u, -1)" 
                                                            class="w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-sm flex items-center justify-center cursor-pointer active:scale-95 transition-transform shadow-2xs">
                                                        -
                                                    </button>
                                                    
                                                    <input type="number" 
                                                           :name="'variant_stok[' + w + '|' + u + ']'" 
                                                           :value="getStok(w, u)" 
                                                           @input="setStok(w, u, $event.target.value)"
                                                           min="0" 
                                                           class="w-14 h-8 text-center bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-900 tabular-nums focus:bg-white focus:outline-none focus:border-slate-400">

                                                    <button type="button" 
                                                            @click="changeStok(w, u, 1)" 
                                                            class="w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-sm flex items-center justify-center cursor-pointer active:scale-95 transition-transform shadow-2xs">
                                                        +
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Empty State Hint --}}
                        <div x-show="warnas.length === 0 || ukurans.length === 0" class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-500 text-[11px] flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-blue-500 text-xs shrink-0"></i>
                            <span>Centang minimal 1 warna dan 1 ukuran untuk mengisi stok fisik awal.</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan: Foto, Penetapan Harga & Aksi (lg:col-span-4) --}}
            <div class="lg:col-span-4 space-y-4">

                {{-- Blok Foto Helm --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
                        <span class="font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-image text-slate-400 text-[11px]"></i>
                            <span>Foto Produk</span>
                        </span>
                        <span class="text-[11px] text-slate-400">Rasio 1:1</span>
                    </div>

                    <div class="p-4 sm:p-5 space-y-3.5 text-xs text-center">
                        {{-- Preview Box --}}
                        <div class="w-32 h-32 rounded-xl bg-slate-50 border border-slate-200 mx-auto flex items-center justify-center overflow-hidden relative shadow-2xs">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" alt="Preview" class="w-full h-full object-contain p-1.5">
                            </template>
                            <template x-if="!imagePreview">
                                <div class="text-slate-400 space-y-1">
                                    <i class="fa-regular fa-image text-2xl"></i>
                                    <p class="text-[10px] text-slate-400">Tanpa Foto</p>
                                </div>
                            </template>
                        </div>

                        {{-- Upload Button & Help --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-center gap-2">
                                <label for="gambar-input" 
                                       class="h-8 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                                    <i class="fa-solid fa-camera text-[10px] text-slate-400"></i>
                                    <span>Pilih Gambar</span>
                                </label>

                                <button type="button" 
                                        x-show="imagePreview"
                                        x-cloak
                                        @click="clearImage" 
                                        class="h-8 px-2.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 text-xs font-medium inline-flex items-center gap-1 transition-colors cursor-pointer">
                                    <span>Batal</span>
                                </button>
                            </div>

                            <input type="file" 
                                   id="gambar-input" 
                                   name="gambar" 
                                   accept="image/png,image/jpeg,image/webp,image/jpg" 
                                   @change="previewImage"
                                   class="hidden">

                            <p class="text-[11px] text-slate-400">
                                PNG, JPG, atau WEBP (maks. 2MB)
                            </p>
                        </div>
                        @error('gambar')
                            <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Blok Harga & Margin --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
                        <span class="font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-tag text-slate-400 text-[11px]"></i>
                            <span>Penetapan Harga</span>
                        </span>
                        <span class="text-[11px] text-slate-400">HPP & Margin</span>
                    </div>

                    <div class="p-4 sm:p-5 space-y-3.5 text-xs">
                        {{-- Harga Modal / HPP --}}
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">
                                Harga Modal / HPP <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" 
                                   name="harga_awal" 
                                   x-model="hargaAwal" 
                                   min="0" 
                                   placeholder="Contoh: 350000" 
                                   required 
                                   class="w-full h-9 px-3 rounded-lg bg-slate-50 border @error('harga_awal') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror text-xs text-slate-900 tabular-nums focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                            @error('harga_awal')
                                <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Harga Jual Konsumen --}}
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700">
                                Harga Jual Konsumen <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" 
                                   name="harga_jual" 
                                   x-model="hargaJual" 
                                   min="0" 
                                   placeholder="Contoh: 450000" 
                                   required 
                                   class="w-full h-9 px-3 rounded-lg bg-slate-50 border @error('harga_jual') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror text-xs text-slate-900 tabular-nums focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                            @error('harga_jual')
                                <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kalkulasi Margin Real-time --}}
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span>Estimasi Margin Kotor:</span>
                                <span class="font-bold text-emerald-600 tabular-nums" x-text="'+' + margin.toLocaleString('id-ID')"></span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-200/60">
                                <span>Persentase Keuntungan:</span>
                                <span class="font-bold text-slate-800 tabular-nums" x-text="marginPercent + '%'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 space-y-2">
                    <button type="submit" 
                            class="w-full h-9 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs shadow-2xs transition-colors cursor-pointer inline-flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-check text-[11px]"></i>
                        <span>Simpan Produk</span>
                    </button>

                    <a href="{{ route('admin.produk.index') }}" 
                       wire:navigate
                       class="w-full h-9 px-4 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 font-medium text-xs transition-colors inline-flex items-center justify-center">
                        Batal
                    </a>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection
