@extends('layouts.admin')

@section('title', 'Edit Produk: ' . $produk->nama_helm)
@section('page-title', 'Edit Produk')

@section('content')
<div class="max-w-4xl mx-auto space-y-4"
     x-data="{
        warnas: {{ Js::from(old('warna', $existing_warnas ?? [])) }},
        ukurans: {{ Js::from(old('ukuran', $existing_ukurans ?? [])) }},
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

    {{-- Header & Breadcrumb --}}
    <div class="flex items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.produk.index') }}" 
               class="text-xs text-slate-500 hover:text-slate-800 font-medium inline-flex items-center gap-1.5 transition-colors mb-1">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali ke Katalog</span>
            </a>
            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Edit Produk Helm: {{ $produk->nama_helm }}
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Perbarui rincian spesifikasi, harga jual, foto helm, dan penyesuaian varian fisik
            </p>
        </div>

        <a href="{{ route('katalog.detail', $produk->id_helm) }}" 
           target="_blank"
           class="h-8 px-3 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-medium inline-flex items-center gap-1.5 transition-colors self-start sm:self-auto">
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            <span>Lihat di Web</span>
        </a>
    </div>

    {{-- Main Edit Form --}}
    <form method="POST" action="{{ route('admin.produk.update', $produk->id_helm) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Section 1: Informasi Produk --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-info"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Informasi Utama Produk
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                {{-- Nama Helm --}}
                <div class="sm:col-span-2">
                    <label class="block font-medium text-slate-700 mb-1">
                        Nama Model Helm <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_helm" 
                           value="{{ old('nama_helm', $produk->nama_helm) }}" 
                           required 
                           class="w-full h-9 px-3 bg-white border @error('nama_helm') border-rose-300 ring-1 ring-rose-200 @else border-slate-200/90 @enderror rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
                    @error('nama_helm')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Merk --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Merk Helm <span class="text-rose-500">*</span>
                    </label>
                    <select name="id_merk" 
                            required 
                            class="w-full h-9 px-3 bg-white border @error('id_merk') border-rose-300 @else border-slate-200/90 @enderror rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                        <option value="">-- Pilih Merk --</option>
                        @foreach ($merks as $m)
                            <option value="{{ $m->id_merk }}" {{ old('id_merk', $produk->id_merk) == $m->id_merk ? 'selected' : '' }}>
                                {{ $m->nama_merk }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_merk')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Kategori Helm <span class="text-rose-500">*</span>
                    </label>
                    <select name="id_kategori" 
                            required 
                            class="w-full h-9 px-3 bg-white border @error('id_kategori') border-rose-300 @else border-slate-200/90 @enderror rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategoris as $k)
                            <option value="{{ $k->id_kategori }}" {{ old('id_kategori', $produk->id_kategori) == $k->id_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_kategori')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi Produk --}}
                <div class="sm:col-span-2">
                    <label class="block font-medium text-slate-700 mb-1">
                        Deskripsi & Spesifikasi Produk
                    </label>
                    <textarea name="deskripsi" 
                              rows="3" 
                              placeholder="Keterangan material shell, sertifikasi SNI/DOT, ventilasi, visor, dll..." 
                              class="w-full p-3 bg-white border border-slate-200/90 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Penetapan Harga & Margin --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-tag"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Penetapan Harga & Margin
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                {{-- Harga Beli / Modal --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Harga Modal / Beli (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-medium text-xs">Rp</span>
                        <input type="number" 
                               name="harga_awal" 
                               x-model="hargaAwal" 
                               min="0" 
                               required 
                               class="w-full h-9 pl-9 pr-3 bg-white border @error('harga_awal') border-rose-300 ring-1 ring-rose-200 @else border-slate-200/90 @enderror rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
                    </div>
                    @error('harga_awal')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga Jual Konsumen --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Harga Jual Konsumen (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-medium text-xs">Rp</span>
                        <input type="number" 
                               name="harga_jual" 
                               x-model="hargaJual" 
                               min="0" 
                               required 
                               class="w-full h-9 pl-9 pr-3 bg-white border @error('harga_jual') border-rose-300 ring-1 ring-rose-200 @else border-slate-200/90 @enderror rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
                    </div>
                    @error('harga_jual')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Kalkulasi Margin Real-time --}}
            <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-lg flex items-center justify-between text-xs">
                <div>
                    <span class="text-[11px] text-slate-500 block">Estimasi Keuntungan Kotor (Margin)</span>
                    <span class="font-bold text-emerald-700 text-sm font-mono" x-text="'Rp ' + margin.toLocaleString('id-ID')"></span>
                </div>
                <div class="text-right">
                    <span class="text-[11px] text-slate-500 block">Margin Persentase</span>
                    <span class="font-bold text-slate-800 text-sm font-mono" x-text="marginPercent + '%'"></span>
                </div>
            </div>
        </div>

        {{-- Section 3: Foto Produk --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Foto Helm
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                {{-- Preview Box --}}
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl bg-slate-50 border border-slate-200/80 overflow-hidden flex items-center justify-center shrink-0 mx-auto sm:mx-0">
                    <img :src="imagePreview" alt="Foto Helm" class="w-full h-full object-cover">
                </div>

                {{-- Upload Input --}}
                <div class="sm:col-span-2 space-y-1.5 text-xs">
                    <label class="block font-medium text-slate-700">
                        Ganti Berkas Gambar (Opsional)
                    </label>
                    <input type="file" 
                           name="gambar" 
                           accept="image/png,image/jpeg,image/webp,image/jpg" 
                           @change="previewImage"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-slate-900 file:text-white hover:file:bg-slate-800 file:cursor-pointer cursor-pointer border border-slate-200 rounded-lg p-1.5">
                    <p class="text-[11px] text-slate-400">
                        Biarkan kosong jika tidak ingin mengubah foto produk yang sudah ada.
                    </p>
                    @error('gambar')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 4: Varian Warna, Ukuran & Stok Fisik --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Varian Warna & Ukuran
                    </h3>
                </div>
                <span class="text-[11px] text-slate-400">Sesuaikan kombinasi etalase</span>
            </div>

            {{-- 1. Pilihan Warna --}}
            <div class="space-y-2">
                <label class="block font-semibold text-slate-800 text-xs">
                    Pilihan Warna Helm
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($warnas as $w)
                        <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs cursor-pointer select-none transition-colors"
                               :class="warnas.includes('{{ $w->nama_warna }}') ? 'bg-slate-900 text-white border-slate-900 font-semibold' : 'bg-slate-50 text-slate-700 border-slate-200/80 hover:bg-slate-100'">
                            <input type="checkbox" 
                                   name="warna[]" 
                                   value="{{ $w->nama_warna }}" 
                                   class="sr-only"
                                   @change="toggleWarna('{{ $w->nama_warna }}')"
                                   :checked="warnas.includes('{{ $w->nama_warna }}')">
                            <span class="w-3 h-3 rounded-full border border-slate-300 shadow-2xs shrink-0" style="background-color: {{ $w->kode_hex ?: '#1e293b' }};"></span>
                            <span>{{ $w->nama_warna }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 2. Pilihan Ukuran --}}
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <label class="block font-semibold text-slate-800 text-xs">
                    Pilihan Ukuran Helm
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $u)
                        <label class="inline-flex items-center justify-center w-12 h-9 rounded-lg border text-xs font-mono font-bold cursor-pointer select-none transition-colors"
                               :class="ukurans.includes('{{ $u }}') ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200/80 hover:bg-slate-100'">
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
            <div class="pt-3 border-t border-slate-100 space-y-2" x-show="warnas.length > 0 && ukurans.length > 0" x-cloak>
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Penyesuaian Stok per Varian</h4>
                        <p class="text-[11px] text-slate-400">Nilai tersimpan mencerminkan data fisik saat ini. Ubah kuantitas jika ada penyesuaian.</p>
                    </div>
                </div>

                <div class="border border-slate-200/90 rounded-xl overflow-x-auto shadow-2xs">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-medium">
                                <th class="py-2.5 px-3">Warna \ Ukuran</th>
                                <template x-for="u in ukurans" :key="u">
                                    <th class="py-2.5 px-3 text-center font-mono font-bold text-slate-900" x-text="u"></th>
                                </template>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <template x-for="w in warnas" :key="w">
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold text-slate-800 whitespace-nowrap" x-text="w"></td>
                                    <template x-for="u in ukurans" :key="u">
                                        <td class="py-2 px-3 text-center">
                                            <input type="number" 
                                                   :name="'variant_stok[' + w + '|' + u + ']'" 
                                                   :value="getStok(w, u)" 
                                                   min="0" 
                                                   class="w-16 h-8 text-center bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-bold text-slate-800 focus:outline-none focus:bg-white focus:ring-1 focus:ring-slate-900">
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="warnas.length === 0 || ukurans.length === 0" class="p-3 bg-amber-50 border border-amber-200/80 rounded-lg text-amber-800 text-[11px]">
                Minimal 1 warna dan 1 ukuran harus aktif untuk mempertahankan etalase varian produk.
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-2.5 pt-2">
            <a href="{{ route('admin.produk.index') }}" 
               class="h-9 px-4 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-medium text-xs transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="h-9 px-5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                <i class="fa-solid fa-check text-[11px]"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>

</div>
@endsection
