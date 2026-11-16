@extends('layouts.admin')

@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    {{-- Header --}}
    <div>
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Pengaturan Toko
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">
            Kelola identitas, kontak, dan logo toko
        </p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden"
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
             },
             clearImage() {
                 this.previewUrl = null;
                 const input = document.getElementById('logo-input');
                 if (input) input.value = '';
             }
         }">

        <form method="POST" 
              action="{{ route('admin.pengaturan.update') }}" 
              enctype="multipart/form-data" 
              class="p-4 sm:p-6 space-y-5 text-xs">
            @csrf
            @method('PUT')

            {{-- Logo Section --}}
            <div class="space-y-2">
                <label class="block font-semibold text-slate-700">
                    Logo Toko
                </label>

                <div class="flex items-center gap-4">
                    {{-- Logo Preview Box --}}
                    <div class="w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" alt="Preview" class="w-full h-full object-contain p-1">
                        </template>

                        <template x-if="!previewUrl">
                            <div>
                                @if ($toko && $toko->logo_url)
                                    <img src="{{ $toko->logo_url }}" alt="Logo" class="w-full h-full object-contain p-1">
                                @else
                                    <span class="text-xs font-bold text-slate-400">BPH</span>
                                @endif
                            </div>
                        </template>
                    </div>

                    {{-- Logo Actions --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <label for="logo-input" 
                                   class="h-8 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                                <i class="fa-solid fa-camera text-[10px] text-slate-400"></i>
                                <span>Pilih Logo</span>
                            </label>

                            <button type="button" 
                                    x-show="previewUrl"
                                    x-cloak
                                    @click="clearImage" 
                                    class="h-8 px-2.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 text-xs font-medium inline-flex items-center gap-1 transition-colors cursor-pointer">
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
                            PNG, JPG, atau WEBP (maks. 2MB)
                        </p>
                    </div>
                </div>
                @error('logo')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama Toko --}}
            <div class="space-y-1.5">
                <label for="nama_toko" class="block font-semibold text-slate-700">
                    Nama Toko
                </label>
                <input type="text" 
                       id="nama_toko"
                       name="nama_toko" 
                       value="{{ old('nama_toko', $toko->nama_toko ?? '') }}"
                       required
                       placeholder="Bayeng Putra Helm"
                       class="w-full h-9 px-3 rounded-lg bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-slate-900 transition-colors @error('nama_toko') border-rose-300 ring-1 ring-rose-400 @enderror">
                @error('nama_toko')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nomor Telepon --}}
            <div class="space-y-1.5">
                <label for="telepon" class="block font-semibold text-slate-700">
                    Nomor Telepon
                </label>
                <input type="text" 
                       id="telepon"
                       name="telepon" 
                       value="{{ old('telepon', $toko->telepon ?? '') }}"
                       required
                       placeholder="089664269624"
                       class="w-full h-9 px-3 rounded-lg bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-slate-900 transition-colors tabular-nums @error('telepon') border-rose-300 ring-1 ring-rose-400 @enderror">
                @error('telepon')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat Gerai --}}
            <div class="space-y-1.5">
                <label for="alamat" class="block font-semibold text-slate-700">
                    Alamat Gerai
                </label>
                <textarea id="alamat"
                          name="alamat" 
                          rows="3" 
                          required
                          placeholder="Jl. RE Martadinata No.49, Cijoho, Kuningan, Jawa Barat"
                          class="w-full p-2.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-slate-900 transition-colors leading-relaxed @error('alamat') border-rose-300 ring-1 ring-rose-400 @enderror">{{ old('alamat', $toko->alamat ?? '') }}</textarea>
                @error('alamat')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Action --}}
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" 
                        class="h-8.5 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-check text-[10px]"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
