@extends('layouts.admin')

@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')

@section('content')
<div class="space-y-4 max-w-5xl mx-auto"
     x-data="{
         namaToko: '{{ addslashes(old('nama_toko', $toko->nama_toko ?? 'Bayeng Putra Helm')) }}',
         telepon: '{{ addslashes(old('telepon', $toko->telepon ?? '')) }}',
         alamat: '{{ addslashes(old('alamat', $toko->alamat ?? '')) }}',
         previewUrl: null,
         previewImage(e) {
             const file = e.target.files[0];
             if (file) {
                 const reader = new FileReader();
                 reader.onload = (ev) => { this.previewUrl = ev.target.result; };
                 reader.readAsDataURL(file);
             } else {
                 this.previewUrl = null;
             }
         },
         clearImage() {
             this.previewUrl = null;
             const input = document.getElementById('logo-input');
             if (input) input.value = '';
         }
     }">

    {{-- 1. Header Minimalist --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Pengaturan Toko
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Identitas resmi gerai yang tercetak pada nota kasir, kop laporan, dan invoice
            </p>
        </div>

        <div class="text-[11px] text-slate-400">
            Terakhir diperbarui {{ $toko && $toko->updated_at ? $toko->updated_at->translatedFormat('d M Y, H:i') : 'hari ini' }}
        </div>
    </div>

    {{-- 2. Grid Form & Live Preview --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Form Kolom Kiri (lg:col-span-7) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
                <span class="font-semibold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-store text-slate-400 text-[11px]"></i>
                    <span>Informasi Gerai</span>
                </span>
                <span class="text-[11px] text-slate-400">
                    Wajib diisi lengkap
                </span>
            </div>

            <form method="POST" 
                  action="{{ route('admin.pengaturan.update') }}" 
                  enctype="multipart/form-data" 
                  class="p-4 sm:p-5 space-y-4 text-xs">
                @csrf
                @method('PUT')

                {{-- Nama Toko --}}
                <div class="space-y-1">
                    <label for="nama_toko" class="block font-semibold text-slate-700">
                        Nama Toko
                    </label>
                    <input type="text" 
                           id="nama_toko"
                           name="nama_toko" 
                           x-model="namaToko"
                           required
                           placeholder="Bayeng Putra Helm"
                           class="w-full h-9 px-3 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors @error('nama_toko') border-rose-300 ring-1 ring-rose-400 @enderror">
                    @error('nama_toko')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor Telepon --}}
                <div class="space-y-1">
                    <label for="telepon" class="block font-semibold text-slate-700">
                        Nomor Telepon
                    </label>
                    <input type="text" 
                           id="telepon"
                           name="telepon" 
                           x-model="telepon"
                           required
                           placeholder="089664269624"
                           class="w-full h-9 px-3 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors tabular-nums @error('telepon') border-rose-300 ring-1 ring-rose-400 @enderror">
                    @error('telepon')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alamat Toko --}}
                <div class="space-y-1">
                    <label for="alamat" class="block font-semibold text-slate-700">
                        Alamat Gerai
                    </label>
                    <textarea id="alamat"
                              name="alamat" 
                              rows="3" 
                              x-model="alamat"
                              required
                              placeholder="Jl. RE Martadinata No.49, Cijoho, Kuningan, Jawa Barat"
                              class="w-full p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors leading-relaxed @error('alamat') border-rose-300 ring-1 ring-rose-400 @enderror"></textarea>
                    @error('alamat')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Logo Toko --}}
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block font-semibold text-slate-700">
                        Logo Gerai
                    </label>

                    <div class="flex items-center gap-3.5">
                        {{-- Logo Preview Box --}}
                        <div class="w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center shrink-0 overflow-hidden relative shadow-2xs">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Preview Baru" class="w-full h-full object-contain p-1">
                            </template>

                            <template x-if="!previewUrl">
                                <div>
                                    @if ($toko && $toko->logo_url)
                                        <img src="{{ $toko->logo_url }}" alt="Logo Gerai" class="w-full h-full object-contain p-1">
                                    @else
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">BPH</span>
                                    @endif
                                </div>
                            </template>
                        </div>

                        {{-- File Input & Actions --}}
                        <div class="flex-1 space-y-1.5">
                            <div class="flex items-center gap-2">
                                <label for="logo-input" 
                                       class="h-8 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                                    <i class="fa-solid fa-arrow-up-from-bracket text-[10px]"></i>
                                    <span>Pilih Gambar</span>
                                </label>

                                <button type="button" 
                                        x-show="previewUrl"
                                        x-cloak
                                        @click="clearImage" 
                                        class="h-8 px-2.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 text-xs font-medium inline-flex items-center gap-1 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                    <span>Batal</span>
                                </button>
                            </div>

                            <input type="file" 
                                   id="logo-input" 
                                   name="logo" 
                                   accept="image/jpeg,image/png,image/webp" 
                                   @change="previewImage"
                                   class="hidden">

                            <p class="text-[11px] text-slate-400">
                                Format PNG, JPG, atau WEBP (maksimal 2MB)
                            </p>
                        </div>
                    </div>
                    @error('logo')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Action --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" 
                            class="h-8.5 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                        <i class="fa-solid fa-check text-[10px]"></i>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Kolom Kanan: Pratinjau Dokumen & Struk Kasir (lg:col-span-5) --}}
        <div class="lg:col-span-5 space-y-3">
            {{-- Preview Box (Simulasi Nota Kasir / Struk) --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
                    <span class="font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-slate-400 text-[11px]"></i>
                        <span>Pratinjau Nota Kasir</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium">Struk Termal</span>
                </div>

                <div class="p-4 sm:p-5 bg-slate-50/40 text-center space-y-2.5">
                    {{-- Logo Simulation --}}
                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-200/80 mx-auto flex items-center justify-center overflow-hidden p-1 shadow-2xs">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" alt="Logo" class="w-full h-full object-contain">
                        </template>
                        <template x-if="!previewUrl">
                            <div>
                                @if ($toko && $toko->logo_url)
                                    <img src="{{ $toko->logo_url }}" alt="Logo" class="w-full h-full object-contain">
                                @else
                                    <span class="text-xs font-bold text-slate-500">BPH</span>
                                @endif
                            </div>
                        </template>
                    </div>

                    {{-- Nama Toko Simulation --}}
                    <div>
                        <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase" x-text="namaToko || 'Bayeng Putra Helm'"></h2>
                        <p class="text-[11px] text-slate-500 leading-snug mt-1 max-w-xs mx-auto" x-text="alamat || 'Alamat toko belum diisi'"></p>
                        <p class="text-[10px] text-slate-400 tabular-nums mt-0.5" x-text="'Telp: ' + (telepon || '-')"></p>
                    </div>

                    {{-- Receipt Mock Body --}}
                    <div class="pt-2 border-t border-dashed border-slate-200 text-left text-[11px] space-y-1 text-slate-500">
                        <div class="flex justify-between text-[10px]">
                            <span>No: ORD-20261116-0001</span>
                            <span>{{ now()->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between font-medium text-slate-800 pt-1">
                            <span class="truncate">KYT NF-R Solid Black</span>
                            <span class="tabular-nums">1.500.000</span>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-400">
                            <span>1x @ 1.500.000</span>
                            <span class="text-emerald-600 font-semibold">Lunas</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Strip Penggunaan Data --}}
            <div class="p-3.5 rounded-xl border border-slate-200/90 bg-white text-xs text-slate-600 space-y-1.5 shadow-2xs">
                <span class="font-semibold text-slate-800 flex items-center gap-1.5 text-[11px]">
                    <i class="fa-solid fa-circle-info text-blue-500 text-[11px]"></i>
                    <span>Penyelarasan Sistem Otomatis</span>
                </span>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Perubahan nama, alamat, telepon, dan logo di halaman ini langsung tersinkronkan ke kop ekspor PDF laporan penjualan, struk kasir termal, invoice pelanggan, dan footer situs.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection
