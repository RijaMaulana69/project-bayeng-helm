@extends('layouts.admin')

@section('title', 'Edit Produk: ' . $produk->nama_helm)
@section('page-title', 'Edit Produk')

@section('content')
<div class="max-w-2xl mx-auto space-y-4"
     x-data="{
        warnas: {{ Js::from(old('warna', $existing_warnas ?? [])) }},
        ukurans: {{ Js::from(old('ukuran', $existing_ukurans ?? [])) }},
        warnaColors: {{ Js::from($warnas->pluck('kode_hex', 'nama_warna')) }},
        variantData: {{ Js::from(old('variant_stok', $variant_data ?? [])) }},
        hargaAwal: '{{ old('harga_awal', $produk->harga_awal) }}',
        hargaJual: '{{ old('harga_jual', $produk->harga_jual) }}',
        imagePreview: '{{ $produk->gambar_url }}',
        previewImage(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => { this.imagePreview = ev.target.result; };
                reader.readAsDataURL(file);
            }
        },
        clearImage() {
            this.imagePreview = '{{ $produk->gambar_url }}';
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
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.produk.index') }}" 
               wire:navigate
               class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-900 font-medium transition-colors mb-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali ke Katalog</span>
            </a>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Edit Produk Helm: {{ $produk->nama_helm }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Perbarui rincian katalog, harga modal &amp; jual, foto, serta inventori stok varian
            </p>
        </div>

        <a href="{{ route('katalog.detail', $produk->id_helm) }}" 
           target="_blank"
           class="h-8 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium inline-flex items-center gap-1.5 shadow-2xs transition-colors self-start sm:self-auto">
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
            <span>Lihat di Web</span>
        </a>
    </div>

    {{-- 2. Single Unified Form Card (Rekomendasi 1: Clean & Mobile-First) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <form method="POST" action="{{ route('admin.produk.update', $produk->id_helm) }}" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-5 text-xs">
            @csrf
            @method('PUT')

            {{-- Section A: Foto Produk & Nama Model (Compact Flex) --}}
            <div class="flex flex-col sm:flex-row gap-4 items-start">
                {{-- Foto Uploader (Compact Box) --}}
                <div class="shrink-0 space-y-1.5 self-center sm:self-start text-center">
                    <div class="w-20 h-20 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center overflow-hidden relative shadow-2xs">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" alt="Preview" class="w-full h-full object-contain p-1">
                        </template>
                        <template x-if="!imagePreview">
                            <div class="text-slate-400 text-center space-y-0.5">
                                <i class="fa-regular fa-image text-lg"></i>
                                <p class="text-[9px] text-slate-400">Foto</p>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center justify-center gap-1">
                        <label for="gambar-input" 
                               class="h-7 px-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-[11px] font-medium inline-flex items-center gap-1 shadow-2xs transition-colors cursor-pointer">
                            <i class="fa-solid fa-camera text-[9px] text-slate-400"></i>
                            <span>Ganti</span>
                        </label>
                        <button type="button" 
                                x-show="imagePreview !== '{{ $produk->gambar_url }}'" 
                                x-cloak 
                                @click="clearImage" 
                                class="h-7 px-1.5 rounded-md border border-rose-200 hover:bg-rose-50 text-rose-600 text-[11px] font-medium transition-colors cursor-pointer">
                            Reset
                        </button>
                    </div>
                    <input type="file" 
                           id="gambar-input" 
                           name="gambar" 
                           accept="image/png,image/jpeg,image/webp,image/jpg" 
                           @change="previewImage" 
                           class="hidden">
                    <p class="text-[10px] text-slate-400 max-w-[100px] leading-tight">Maks. 2MB</p>
                    @error('gambar')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Model Helm --}}
                <div class="flex-1 w-full space-y-1">
                    <label class="block font-semibold text-slate-700">
                        Nama Model Helm <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_helm" 
                           value="{{ old('nama_helm', $produk->nama_helm) }}" 
                           placeholder="Contoh: KYT TT Course Tourist Doff" 
                           required 
                           class="w-full h-10 px-3 rounded-xl bg-slate-50 border @error('nama_helm') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                    @error('nama_helm')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-slate-400">Sertakan seri, motif, atau varian grafis helm</p>
                </div>
            </div>

            {{-- Section B: Merk & Kategori Helm (2 Kolom) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-3 border-t border-slate-100">
                <div class="space-y-1">
                    <label class="block font-semibold text-slate-700">
                        Merk Helm <span class="text-rose-500">*</span>
                    </label>
                    <select name="id_merk" 
                            required 
                            class="w-full h-10 px-3 rounded-xl bg-slate-50 border @error('id_merk') border-rose-300 @else border-slate-200 @enderror text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400 cursor-pointer">
                        <option value="">-- Pilih Merk --</option>
                        @foreach ($merks as $m)
                            <option value="{{ $m->id_merk }}" {{ old('id_merk', $produk->id_merk) == $m->id_merk ? 'selected' : '' }}>
                                {{ $m->nama_merk }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_merk')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="block font-semibold text-slate-700">
                        Kategori Helm <span class="text-rose-500">*</span>
                    </label>
                    <select name="id_kategori" 
                            required 
                            class="w-full h-10 px-3 rounded-xl bg-slate-50 border @error('id_kategori') border-rose-300 @else border-slate-200 @enderror text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400 cursor-pointer">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategoris as $k)
                            <option value="{{ $k->id_kategori }}" {{ old('id_kategori', $produk->id_kategori) == $k->id_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_kategori')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Section C: Penetapan Harga & Kalkulasi Margin --}}
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1">
                        <label class="block font-semibold text-slate-700">
                            Harga Modal / HPP (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" 
                               name="harga_awal" 
                               x-model="hargaAwal" 
                               min="0" 
                               placeholder="Contoh: 350000" 
                               required 
                               class="w-full h-10 px-3 rounded-xl bg-slate-50 border @error('harga_awal') border-rose-300 @else border-slate-200 @enderror text-xs text-slate-900 tabular-nums focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                        @error('harga_awal')
                            <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block font-semibold text-slate-700">
                            Harga Jual Konsumen (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" 
                               name="harga_jual" 
                               x-model="hargaJual" 
                               min="0" 
                               placeholder="Contoh: 450000" 
                               required 
                               class="w-full h-10 px-3 rounded-xl bg-slate-50 border @error('harga_jual') border-rose-300 @else border-slate-200 @enderror text-xs text-slate-900 tabular-nums focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                        @error('harga_jual')
                            <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Margin Helper Teks Halus (Bukan Kotak AI-Slop) --}}
                <div class="flex items-center justify-between text-[11px] text-slate-500 px-1" x-show="Number(hargaJual) > 0" x-cloak>
                    <span>Estimasi laba kotor: <strong class="text-emerald-600 tabular-nums" x-text="'+Rp ' + margin.toLocaleString('id-ID')"></strong></span>
                    <span>Margin: <strong class="text-slate-800 tabular-nums" x-text="marginPercent + '%'"></strong></span>
                </div>
            </div>

            {{-- Section D: Varian Warna, Ukuran & Stok Fisik (Gaya Restok Mobile-First) --}}
            <div class="pt-3 border-t border-slate-100 space-y-3.5">
                {{-- Pilihan Warna --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block font-semibold text-slate-700">
                            Pilihan Warna Helm
                        </label>
                        <span class="text-[11px] text-slate-400" x-text="warnas.length > 0 ? warnas.length + ' warna aktif' : 'Pilih warna'"></span>
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

                {{-- Pilihan Ukuran --}}
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <label class="block font-semibold text-slate-700">
                            Pilihan Ukuran Helm
                        </label>
                        <div class="flex items-center gap-1.5 text-[11px]">
                            <button type="button" 
                                    @click="ukurans = ['M', 'L', 'XL']" 
                                    class="text-blue-600 hover:text-blue-800 font-medium cursor-pointer">
                                + M, L, XL
                            </button>
                            <span class="text-slate-300">·</span>
                            <button type="button" 
                                    @click="ukurans = ['S', 'M', 'L', 'XL', 'XXL']" 
                                    class="text-slate-500 hover:text-slate-800 font-medium cursor-pointer">
                                Semua Size
                            </button>
                        </div>
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

                {{-- Mobile-First Variant Ledger List --}}
                <div class="pt-2 border-t border-slate-100 space-y-2.5" x-show="warnas.length > 0 && ukurans.length > 0" x-cloak>
                    {{-- Toolbar Presets --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="text-[11px] text-slate-600 font-medium">
                            Set Stok Semua Varian:
                        </div>
                        <div class="flex items-center gap-1 flex-wrap">
                            <button type="button" @click="setAllStok(0)" class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 cursor-pointer active:scale-95 transition-transform shadow-2xs">Nol (0)</button>
                            <button type="button" @click="setAllStok(1)" class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 cursor-pointer active:scale-95 transition-transform shadow-2xs">Semua 1</button>
                            <button type="button" @click="setAllStok(3)" class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 cursor-pointer active:scale-95 transition-transform shadow-2xs">Semua 3</button>
                            <button type="button" @click="setAllStok(5)" class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 cursor-pointer active:scale-95 transition-transform shadow-2xs">Semua 5</button>
                            <button type="button" @click="setAllStok(10)" class="px-2 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 cursor-pointer active:scale-95 transition-transform shadow-2xs">Semua 10</button>
                        </div>
                    </div>

                    {{-- Card Ledger Items --}}
                    <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden bg-white">
                        <template x-for="w in warnas" :key="w">
                            <div>
                                <template x-for="u in ukurans" :key="w + '|' + u">
                                    <div class="p-3 hover:bg-slate-50/60 transition-colors flex items-center justify-between gap-3 border-b border-slate-100 last:border-b-0">
                                        {{-- Info Kombinasi --}}
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-3.5 h-3.5 rounded-full border border-black/10 shrink-0 shadow-2xs" 
                                                  :style="'background-color: ' + (warnaColors[w] || '#334155')"></span>
                                            <div class="min-w-0 truncate">
                                                <div class="font-semibold text-xs text-slate-900 truncate flex items-center gap-1.5">
                                                    <span x-text="w"></span>
                                                    <span class="text-slate-300">·</span>
                                                    <span class="font-bold text-slate-700" x-text="'Size ' + u"></span>
                                                </div>
                                                <div class="text-[11px] mt-0.5">
                                                    <span x-show="getStok(w, u) > 0" class="text-emerald-600 font-medium" x-text="getStok(w, u) + ' unit di rak fisik'"></span>
                                                    <span x-show="getStok(w, u) === 0" class="text-rose-500 font-medium">Stok habis (0)</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Stepper Touch --}}
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

                    {{-- Total Counter --}}
                    <div class="flex items-center justify-between px-1 text-[11px] text-slate-500">
                        <span x-text="totalKombinasi + ' varian aktif'"></span>
                        <span>Total stok fisik: <strong class="text-slate-900 font-bold tabular-nums" x-text="totalUnitFisik + ' unit'"></strong></span>
                    </div>
                </div>

                {{-- Empty Hint --}}
                <div x-show="warnas.length === 0 || ukurans.length === 0" class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-500 text-[11px] flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-500 text-xs shrink-0"></i>
                    <span>Minimal 1 warna dan 1 ukuran harus aktif untuk mempertahankan etalase varian produk.</span>
                </div>
            </div>

            {{-- Section E: Deskripsi & Spesifikasi (Opsional) --}}
            <div class="pt-3 border-t border-slate-100 space-y-1">
                <label class="block font-semibold text-slate-700">
                    Deskripsi &amp; Spesifikasi <span class="text-slate-400 font-normal\">(Opsional)</span>
                </label>
                <textarea name="deskripsi" 
                          rows="3" 
                          placeholder="Keterangan bahan shell, sertifikasi SNI/DOT, tipe pengunci tali, fitur visor..." 
                          class="w-full p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors leading-relaxed">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2.5">
                <a href="{{ route('admin.produk.index') }}" 
                   wire:navigate
                   class="h-10 px-4 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-medium text-xs transition-colors inline-flex items-center justify-center">
                    Batal
                </a>
                <button type="submit" 
                        class="h-10 px-5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs shadow-2xs transition-colors cursor-pointer inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check text-[11px]"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
