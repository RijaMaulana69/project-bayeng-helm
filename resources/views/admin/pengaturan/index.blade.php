@extends('layouts.admin')

@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')

@section('content')
<div class="max-w-xl mx-auto space-y-3 sm:space-y-4">

    @php
        $tokoData = $toko ?? $pengaturan ?? null;
    @endphp

    {{-- 1. Header Minimalist --}}
    <div>
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Pengaturan Toko
        </h1>
        <p class="text-xs text-slate-500">
            Identitas resmi gerai untuk nota, kop surat, dan laporan PDF
        </p>
    </div>

    {{-- 2. Form Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0">
                <i class="fa-solid fa-store text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900">
                    Profil Identitas Gerai
                </h2>
                <p class="text-xs text-slate-400">
                    Informasi ini dicantumkan pada kop resmi laporan penjualan
                </p>
            </div>
        </div>

        <form method="POST" 
              action="{{ route('admin.pengaturan.update') }}" 
              enctype="multipart/form-data" 
              x-data="{ 
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
                  }
              }"
              class="p-4 sm:p-5 space-y-4 text-xs">
            @csrf
            @method('PUT')

            {{-- Nama Toko --}}
            <div class="space-y-1">
                <label for="nama_toko" class="block font-semibold text-slate-700">
                    Nama Toko / Usaha <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                        <i class="fa-solid fa-shop text-[11px]"></i>
                    </span>
                    <input type="text" 
                           id="nama_toko"
                           name="nama_toko" 
                           value="{{ old('nama_toko', $tokoData->nama_toko ?? '') }}" 
                           required
                           placeholder="contoh: Bayeng Putra Helm"
                           class="w-full h-10 pl-9 pr-3.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all @error('nama_toko') border-rose-300 ring-1 ring-rose-400 @enderror">
                </div>
                @error('nama_toko')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nomor Telepon / WhatsApp --}}
            <div class="space-y-1">
                <label for="telepon" class="block font-semibold text-slate-700">
                    Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                        <i class="fa-solid fa-phone text-[11px]"></i>
                    </span>
                    <input type="text" 
                           id="telepon"
                           name="telepon" 
                           value="{{ old('telepon', $tokoData->telepon ?? '') }}" 
                           required
                           placeholder="contoh: 081234567890"
                           class="w-full h-10 pl-9 pr-3.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all font-mono @error('telepon') border-rose-300 ring-1 ring-rose-400 @enderror">
                </div>
                @error('telepon')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat Toko --}}
            <div class="space-y-1">
                <label for="alamat" class="block font-semibold text-slate-700">
                    Alamat Lengkap Gerai <span class="text-rose-500">*</span>
                </label>
                <textarea id="alamat"
                          name="alamat" 
                          rows="3" 
                          required
                          placeholder="contoh: Jl. RE Martadinata No. 49, Kuningan"
                          class="w-full p-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all leading-relaxed @error('alamat') border-rose-300 ring-1 ring-rose-400 @enderror">{{ old('alamat', $tokoData->alamat ?? '') }}</textarea>
                @error('alamat')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Logo Toko --}}
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <label class="block font-semibold text-slate-700">
                    Logo Resmi Toko
                </label>

                <div class="flex items-center gap-3.5">
                    {{-- Current / Preview Box --}}
                    <div class="w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center shrink-0 overflow-hidden relative shadow-2xs">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" alt="Preview Logo" class="w-full h-full object-contain p-1">
                        </template>

                        <template x-if="!previewUrl">
                            <div>
                                @if (!empty($tokoData->logo))
                                    @php
                                        $logoPath = str_starts_with($tokoData->logo, 'uploads/') 
                                            ? asset($tokoData->logo) 
                                            : asset('storage/' . $tokoData->logo);
                                    @endphp
                                    <img src="{{ $logoPath }}" alt="Logo Saat Ini" class="w-full h-full object-contain p-1">
                                @else
                                    <i class="fa-solid fa-image text-slate-300 text-xl"></i>
                                @endif
                            </div>
                        </template>
                    </div>

                    {{-- File Input --}}
                    <div class="flex-1 space-y-1">
                        <input type="file" 
                               name="logo" 
                               id="input-logo"
                               accept="image/jpeg,image/png,image/webp" 
                               @change="previewImage"
                               class="block w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-slate-200 file:text-xs file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 cursor-pointer">
                        <p class="text-[11px] text-slate-400">
                            Format: PNG, JPG, atau WEBP. Maksimal 2MB.
                        </p>
                    </div>
                </div>
                @error('logo')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Action --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" 
                        class="h-10 px-5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold inline-flex items-center justify-center gap-2 shadow-2xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
