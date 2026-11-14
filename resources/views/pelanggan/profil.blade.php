@extends('layouts.pelanggan')
@section('title', 'Profil & Pengaturan Akun')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-slate-800 font-semibold">Pengaturan Akun</span>
        </nav>

        {{-- Profile Header Banner --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-500 text-white flex items-center justify-center font-black text-2xl uppercase shadow-lg shadow-blue-500/20">
                    {{ substr($pelanggan->nama, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $pelanggan->nama }}</h1>
                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-wider">Pelanggan Aktif</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                        <span><i class="fa-solid fa-envelope text-slate-400 mr-1"></i> {{ $pelanggan->email }}</span>
                        <span class="text-slate-300">&bull;</span>
                        <span><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $pelanggan->telepon ?? '-' }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('pelanggan.pesanan') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    <i class="fa-solid fa-receipt text-blue-600"></i>
                    <span>Riwayat Pesanan</span>
                </a>
            </div>
        </div>

        {{-- Profile Form --}}
        <form method="POST" action="{{ route('pelanggan.profil.update') }}" class="space-y-6" x-data="{ showPass: false }">
            @csrf
            @method('PUT')

            {{-- Card 1: Data Diri --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-5">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Informasi Kontak & Akun</h2>
                        <p class="text-[11px] text-slate-400">Data ini digunakan untuk verifikasi e-Tiket pengambilan di kasir toko.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Email (Read-only) --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Email (Akun Login)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </div>
                            <input type="email"
                                   value="{{ $pelanggan->email }}"
                                   readonly
                                   disabled
                                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100/70 text-xs font-semibold text-slate-500 cursor-not-allowed">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Alamat email terdaftar dan tidak dapat diganti secara mandiri demi keamanan akun.</p>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="nama"
                               name="nama"
                               value="{{ old('nama', $pelanggan->nama) }}"
                               required
                               placeholder="Nama sesuai KTP"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('nama')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No Telepon --}}
                    <div>
                        <label for="telepon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="telepon"
                               name="telepon"
                               value="{{ old('telepon', $pelanggan->telepon) }}"
                               required
                               placeholder="08xxxxxxxxxx"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('telepon')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Card 2: Keamanan Akun / Ubah Kata Sandi --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-5">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Perbarui Kata Sandi</h2>
                        <p class="text-[11px] text-slate-400">Biarkan formulir ini kosong jika Anda tidak berencana mengubah kata sandi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   placeholder="••••••••"
                                   class="w-full px-4 pr-10 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <button type="button" 
                                    @click="showPass = !showPass" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fa-solid text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[10px] text-slate-400 mt-1">Min. 8 karakter (huruf besar, kecil, angka).</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ulangi Kata Sandi Baru
                        </label>
                        <input :type="showPass ? 'text' : 'password'"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Konfirmasi kata sandi"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>
            </div>

            {{-- Submit Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-blue-600/20 active:scale-98 transition-all">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Perubahan Akun</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
