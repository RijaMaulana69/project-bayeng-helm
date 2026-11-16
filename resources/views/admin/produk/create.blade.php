@extends('layouts.admin')

@section('title', 'Tambah Helm')
@section('page-title', 'Tambah Helm')

@section('content')
<div class="max-w-3xl mx-auto space-y-4 sm:space-y-5 pb-8"
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

    {{-- 1. Top Navigation & Header --}}
    <div>
        <a href="{{ route('admin.produk.index') }}" 
           wire:navigate
           class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-900 font-medium transition-colors mb-2">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali</span>
        </a>
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Tambah Helm
        </h1>
    </div>

    {{-- 2. Form Container --}}
    <form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-5 text-xs">
        @csrf

        {{-- Card 1: Informasi Helm & Foto --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">
                Informasi Helm
            </h2>

            {{-- Foto & Detail Helm --}}
            <div class="flex flex-col sm:flex-row gap-4 items-start">
                {{-- Foto Uploader --}}
                <div class="w-full sm:w-auto shrink-0 flex flex-col items-center sm:items-start gap-1.5">
                    <label class="block font-semibold text-slate-700 text-xs self-start">
                        Foto
                    </label>
                    <div class="w-full sm:w-28 h-32 sm:h-28 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 hover:border-slate-300 flex flex-col items-center justify-center overflow-hidden relative transition-colors cursor-pointer group">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" alt="Preview" class="w-full h-full object-contain p-1">
                        </template>
                        <template x-if="!imagePreview">
                            <label for="gambar-input" class="w-full h-full flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                                <i class="fa-solid fa-camera text-base text-slate-400 group-hover:text-slate-600 transition-colors"></i>
                                <span class="text-[11px] font-medium text-slate-600 mt-1">Pilih Foto</span>
                            </label>
                        </template>
                    </div>
                    <div class="flex items-center gap-1.5 w-full justify-center sm:justify-start">
                        <label for="gambar-input" 
                               class="h-7 px-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-[11px] font-medium inline-flex items-center gap-1 shadow-2xs transition-colors cursor-pointer">
                            <span x-text="imagePreview ? 'Ganti' : 'Pilih'"></span>
                        </label>
                        <button type="button" 
                                x-show="imagePreview" 
                                x-cloak 
                                @click="clearImage" 
                                class="h-7 px-2 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 text-[11px] font-medium transition-colors cursor-pointer">
                            Hapus
                        </button>
                    </div>
                    <input type="file" 
                           id="gambar-input" 
                           name="gambar" 
                           accept="image/png,image/jpeg,image/webp,image/jpg" 
                           @change="previewImage" 
                           class="hidden">
                    @error('gambar')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Model Helm & Merk / Kategori --}}
                <div class="flex-1 w-full space-y-3.5">
                    <div class="space-y-1">
                        <label class="block font-semibold text-slate-700 text-xs">
                            Nama Helm <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_helm" 
                               value="{{ old('nama_helm') }}" 
                               placeholder="Contoh: KYT TT Course Tourist Doff" 
                               required 
                               class="w-full h-10 px-3 rounded-xl bg-slate-50 border @error('nama_helm') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                        @error('nama_helm')
                            <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700 text-xs">
                                Merk <span class="text-rose-500">*</span>
                            </label>
                            <select name="id_merk" 
                                    required 
                                    class="w-full h-10 px-3 rounded-xl bg-slate-50 border @error('id_merk') border-rose-300 @else border-slate-200 @enderror text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400 cursor-pointer">
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

                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-700 text-xs">
                                Kategori <span class="text-rose-500">*</span>
                            </label>
                            <select name="id_kategori" 
                                    required 
                                    class="w-full h-10 px-3 rounded-xl bg-slate-50 border @error('id_kategori') border-rose-300 @else border-slate-200 @enderror text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400 cursor-pointer">
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
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="space-y-1 pt-2 border-t border-slate-100">
                <label class="block font-semibold text-slate-700 text-xs">
                    Deskripsi
                </label>
                <textarea name="deskripsi" 
                          rows="3" 
                          placeholder="Bahan shell, sertifikasi, tipe visor..." 
                          class="w-full p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors leading-relaxed">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Card 2: Harga --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">
                Harga
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div class="space-y-1">
                    <label class="block font-semibold text-slate-700 text-xs">
                        Harga Modal (HPP) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs font-semibold text-slate-400">Rp</span>
                        <input type="number" 
                               name="harga_awal" 
                               x-model="hargaAwal" 
                               min="0" 
                               placeholder="0" 
                               required 
                               class="w-full h-10 pl-9 pr-3 rounded-xl bg-slate-50 border @error('harga_awal') border-rose-300 @else border-slate-200 @enderror text-xs text-slate-900 tabular-nums focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                    </div>
                    @error('harga_awal')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="block font-semibold text-slate-700 text-xs">
                        Harga Jual <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs font-semibold text-slate-400">Rp</span>
                        <input type="number" 
                               name="harga_jual" 
                               x-model="hargaJual" 
                               min="0" 
                               placeholder="0" 
                               required 
                               class="w-full h-10 pl-9 pr-3 rounded-xl bg-slate-50 border @error('harga_jual') border-rose-300 @else border-slate-200 @enderror text-xs text-slate-900 tabular-nums focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                    </div>
                    @error('harga_jual')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Estimasi Margin Ringkas --}}
            <div class="text-xs text-slate-600 font-medium pt-0.5" x-show="Number(hargaJual) > 0" x-cloak>
                Estimasi laba: <strong class="text-slate-900 font-bold tabular-nums" x-text="'+Rp ' + margin.toLocaleString('id-ID')"></strong>
                <span class="text-slate-300 mx-1.5">·</span>
                Margin: <strong class="text-slate-900 font-bold tabular-nums" x-text="marginPercent + '%'"></strong>
            </div>
        </div>

        {{-- Card 3: Varian & Stok --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">
                    Varian &amp; Stok
                </h2>
                <div x-show="totalUnitFisik > 0" x-cloak class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold tabular-nums">
                    <span x-text="totalUnitFisik"></span> unit total
                </div>
            </div>

            {{-- Pilihan Warna --}}
            <div class="space-y-2">
                <label class="block font-semibold text-slate-700 text-xs">
                    Warna
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-1.5">
                    @foreach ($warnas as $w)
                        <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-xs cursor-pointer select-none transition-all"
                               :class="warnas.includes('{{ $w->nama_warna }}') ? 'bg-slate-900 text-white border-slate-900 font-medium shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                            <input type="checkbox" 
                                   name="warna[]" 
                                   value="{{ $w->nama_warna }}" 
                                   class="sr-only"
                                   @change="toggleWarna('{{ $w->nama_warna }}')"
                                   :checked="warnas.includes('{{ $w->nama_warna }}')">
                            <span class="w-3 h-3 rounded-full border border-black/10 shrink-0" style="background-color: {{ $w->kode_hex ?: '#334155' }};"></span>
                            <span class="truncate">{{ $w->nama_warna }}</span>
                            <i class="fa-solid fa-check text-[10px] ml-auto shrink-0" x-show="warnas.includes('{{ $w->nama_warna }}')" x-cloak></i>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Pilihan Ukuran --}}
            <div class="space-y-2 pt-3 border-t border-slate-100">
                <label class="block font-semibold text-slate-700 text-xs">
                    Ukuran
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $u)
                        <label class="inline-flex items-center justify-center w-14 h-10 rounded-xl border text-xs font-bold cursor-pointer select-none transition-all tabular-nums"
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

            {{-- Rincian Stok Fisik per Varian --}}
            <div class="pt-3 border-t border-slate-100 space-y-3" x-show="warnas.length > 0 && ukurans.length > 0" x-cloak>
                {{-- Variant Items List (Clean & Touch-friendly) --}}
                <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden bg-white">
                    <template x-for="w in warnas" :key="w">
                        <div>
                            <template x-for="u in ukurans" :key="w + '|' + u">
                                <div class="p-3 sm:px-4 hover:bg-slate-50/60 transition-colors flex items-center justify-between gap-3 border-b border-slate-100 last:border-b-0">
                                    {{-- Varian Info --}}
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-3.5 h-3.5 rounded-full border border-black/10 shrink-0 shadow-2xs" 
                                              :style="'background-color: ' + (warnaColors[w] || '#334155')"></span>
                                        <div class="font-bold text-xs text-slate-900 truncate">
                                            <span x-text="w"></span>
                                            <span class="text-slate-300 mx-1">·</span>
                                            <span class="text-slate-600 font-semibold" x-text="'Size ' + u"></span>
                                        </div>
                                    </div>

                                    {{-- Touch-friendly Stepper --}}
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button" 
                                                @click="changeStok(w, u, -1)" 
                                                class="w-9 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-base flex items-center justify-center cursor-pointer active:scale-95 transition-transform shadow-2xs">
                                            -
                                        </button>
                                        <input type="number" 
                                               :name="'variant_stok[' + w + '|' + u + ']'" 
                                               :value="getStok(w, u)" 
                                               @input="setStok(w, u, $event.target.value)" 
                                               min="0" 
                                               class="w-14 h-9 text-center bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 tabular-nums focus:bg-white focus:outline-none focus:border-slate-400">
                                        <button type="button" 
                                                @click="changeStok(w, u, 1)" 
                                                class="w-9 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-base flex items-center justify-center cursor-pointer active:scale-95 transition-transform shadow-2xs">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Summary Footer --}}
                <div class="flex items-center justify-between px-1 text-xs text-slate-500">
                    <span x-text="totalKombinasi + ' kombinasi'"></span>
                    <span>Total stok: <strong class="text-slate-900 font-bold tabular-nums" x-text="totalUnitFisik + ' unit'"></strong></span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2.5 pt-2">
            <a href="{{ route('admin.produk.index') }}" 
               wire:navigate
               class="h-10 px-5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-medium text-xs transition-colors inline-flex items-center justify-center shadow-2xs">
                Batal
            </a>
            <button type="submit" 
                    class="h-10 px-6 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-2xs transition-colors cursor-pointer inline-flex items-center justify-center">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
