@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')
@section('page-title', 'Tambah Produk Baru')

@section('content')
<div class="space-y-4 max-w-6xl mx-auto"
     x-data="{
        warnas: {{ Js::from(old('warna', [])) }},
        ukurans: {{ Js::from(old('ukuran', [])) }},
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
            return this.variantData[key] !== undefined ? this.variantData[key] : 0;
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

                {{-- Blok 2: Varian Warna, Ukuran & Matriks Stok --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
                        <span class="font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-slate-400 text-[11px]"></i>
                            <span>Varian & Stok Fisik</span>
                        </span>
                        <span class="text-[11px] text-slate-400">Multi-Dimensi</span>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4 text-xs">
                        {{-- 1. Pilihan Warna --}}
                        <div class="space-y-2">
                            <label class="block font-semibold text-slate-700">
                                Pilihan Warna Helm
                            </label>
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
                            <label class="block font-semibold text-slate-700">
                                Pilihan Ukuran Helm
                            </label>
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

                        {{-- 3. Matriks Stok Varian (Dinamis Alpine.js) --}}
                        <div class="pt-3 border-t border-slate-100 space-y-2.5" x-show="warnas.length > 0 && ukurans.length > 0" x-cloak>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-800">Matriks Stok Awal</h4>
                                    <p class="text-[11px] text-slate-400">Tentukan jumlah unit siap jual untuk tiap kombinasi</p>
                                </div>
                            </div>

                            <div class="border border-slate-200 rounded-xl overflow-x-auto shadow-2xs">
                                <table class="w-full text-xs text-left">
                                    <thead>
                                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-medium">
                                            <th class="py-2.5 px-3 font-semibold text-slate-700">Varian Warna</th>
                                            <template x-for="u in ukurans" :key="u">
                                                <th class="py-2.5 px-3 text-center font-bold text-slate-900 tabular-nums" x-text="u"></th>
                                            </template>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        <template x-for="w in warnas" :key="w">
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="py-2.5 px-3 font-medium text-slate-800 whitespace-nowrap" x-text="w"></td>
                                                <template x-for="u in ukurans" :key="u">
                                                    <td class="py-2 px-3 text-center">
                                                        <input type="number" 
                                                               :name="'variant_stok[' + w + '|' + u + ']'" 
                                                               :value="getStok(w, u)" 
                                                               min="0" 
                                                               class="w-14 h-7 text-center bg-slate-50 border border-slate-200 rounded-md text-xs font-semibold text-slate-800 tabular-nums focus:bg-white focus:outline-none focus:border-slate-400">
                                                    </td>
                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
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
