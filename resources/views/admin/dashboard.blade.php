@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Ringkasan Toko')

@section('content')
<div class="space-y-6">

    {{-- 1. Greeting & Quick Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
        <div class="space-y-0.5">
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Halo, {{ $user->nama_lengkap }}
            </h1>
            <p class="text-xs text-slate-500">
                Operasional Toko &bull; {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pickup.index') }}" 
               class="h-9 px-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold inline-flex items-center gap-2 shadow-2xs transition-colors cursor-pointer">
                <i class="fa-solid fa-qrcode text-xs"></i>
                <span>Kasir Pickup</span>
            </a>
            <a href="{{ route('home') }}" target="_blank" 
               class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium inline-flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-400"></i>
                <span>Lihat Toko</span>
            </a>
        </div>
    </div>

    {{-- 2. Penting Hari Ini (Action Required Cards) --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                Penting Hari Ini
            </h2>
            <span class="text-[11px] text-slate-400 font-medium">Prioritas Kasir & Operasional</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            {{-- Siap Diambil di Toko --}}
            <a href="{{ route('admin.pesanan.index', ['status' => 'ready']) }}" 
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-emerald-300 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-600">Siap Diambil di Toko</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-box-open"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-slate-900">{{ $pesanan_siap_ambil }}</span>
                    <span class="text-xs font-medium text-emerald-600 group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-1">
                        <span>Buka Kasir</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
            </a>

            {{-- Pesanan Masuk Hari Ini --}}
            <a href="{{ route('admin.pesanan.index') }}" 
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-blue-300 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-600">Pesanan Masuk Hari Ini</span>
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-receipt"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-slate-900">{{ $pesanan_hari_ini }}</span>
                    <span class="text-xs font-medium text-blue-600 group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
            </a>

            {{-- Stok Kritis --}}
            <a href="{{ route('admin.stok.index') }}" 
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-amber-300 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-600">Stok Kritis (&lt; 5)</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-2xl font-bold {{ $stok_menipis->count() > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                        {{ $stok_menipis->count() }}
                    </span>
                    <span class="text-xs font-medium text-amber-600 group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-1">
                        <span>Update Stok</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
            </a>
        </div>
    </div>

    {{-- 3. Ringkasan Finansial & Katalog --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        {{-- Omzet Hari Ini --}}
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-xs font-medium text-slate-500 block">Omzet Selesai Hari Ini</span>
            <p class="text-xl font-bold text-slate-900 mt-1">
                {{ format_rupiah($omzet_hari_ini) }}
            </p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Pesanan lunas & diserahkan</span>
        </div>

        {{-- Omzet Bulan Ini --}}
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-xs font-medium text-slate-500 block">Omzet Bulan Ini ({{ now()->translatedFormat('F') }})</span>
            <p class="text-xl font-bold text-blue-600 mt-1">
                {{ format_rupiah($omzet_bulan) }}
            </p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Total omzet terverifikasi</span>
        </div>

        {{-- Total Katalog Helm --}}
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-xs font-medium text-slate-500 block">Katalog Helm Aktif</span>
            <p class="text-xl font-bold text-slate-900 mt-1">
                {{ $total_produk }} <span class="text-xs font-normal text-slate-500">model</span>
            </p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Total {{ $total_pesanan }} transaksi tercatat</span>
        </div>
    </div>

    {{-- 4. Grafik Penjualan Bulanan --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-900">Tren Penjualan Bulanan ({{ date('Y') }})</h3>
                <p class="text-[11px] text-slate-500">Total omzet dari transaksi selesai per bulan</p>
            </div>
            @if(Auth::guard('web')->user()->isOwner())
                <a href="{{ route('admin.owner.laporan.penjualan') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
                    <span>Laporan Lengkap</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            @endif
        </div>
        <div class="h-64 sm:h-72 w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- 5. Dua Kolom: Pesanan Terbaru & Stok Menipis --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        {{-- Pesanan Terbaru (7 Cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Pesanan Masuk Terbaru</h3>
                    <p class="text-[11px] text-slate-400">5 transaksi terakhir yang masuk ke sistem</p>
                </div>
                <a href="{{ route('admin.pesanan.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($pesanan_terbaru as $pesanan)
                    @php
                        $statusBadgeClass = match($pesanan->status) {
                            'pending'       => 'bg-amber-50 text-amber-700 border-amber-200/80',
                            'unpaid_pickup' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                            'ready'         => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                            'paid', 'processing' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                            'completed'     => 'bg-slate-100 text-slate-700 border-slate-200',
                            'cancelled'     => 'bg-rose-50 text-rose-700 border-rose-200/80',
                            default         => 'bg-slate-50 text-slate-600 border-slate-200',
                        };

                        $statusBadgeLabel = match($pesanan->status) {
                            'pending'       => 'Menunggu Bayar',
                            'unpaid_pickup' => 'Menunggu Diambil',
                            'ready'         => 'Siap Diambil',
                            'paid'          => 'Sudah Dibayar',
                            'processing'    => 'Sedang Disiapkan',
                            'completed'     => 'Selesai',
                            'cancelled'     => 'Dibatalkan',
                            default         => $pesanan->status_label,
                        };
                    @endphp
                    <div class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50/60 transition-colors">
                        <div class="min-w-0 space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" class="font-mono font-bold text-xs text-blue-600 hover:underline">
                                    #{{ $pesanan->no_pesanan }}
                                </a>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $statusBadgeClass }}">
                                    {{ $statusBadgeLabel }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-700 truncate">
                                {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum') }}
                                <span class="text-slate-400 font-normal">&bull; {{ $pesanan->created_at->format('d M, H:i') }} WIB</span>
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-xs sm:text-sm font-bold text-slate-900 block">
                                {{ format_rupiah($pesanan->grand_total) }}
                            </span>
                            <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" class="text-[11px] font-medium text-slate-400 hover:text-blue-600 transition-colors inline-flex items-center gap-1 mt-0.5">
                                <span>Detail</span>
                                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        Belum ada pesanan masuk.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Peringatan Stok Menipis (5 Cols) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Peringatan Stok Menipis</h3>
                    <p class="text-[11px] text-slate-400">Helm dengan sisa stok fisik &lt; 5 unit</p>
                </div>
                <a href="{{ route('admin.stok.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
                    <span>Atur Stok</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($stok_menipis as $helm)
                    <div class="p-3.5 sm:p-4 flex items-center gap-3 hover:bg-slate-50/60 transition-colors">
                        <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                            @if($helm->gambar_url)
                                <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-helmet-safety text-xs"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-slate-900 truncate">
                                {{ $helm->nama_helm }}
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $helm->merk->nama_merk ?? 'Helm' }} &bull; {{ format_rupiah($helm->harga_jual) }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                                Sisa {{ $helm->stok }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400 space-y-1">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-lg block mb-1"></i>
                        <p class="font-medium text-slate-700">Semua Stok Aman</p>
                        <p class="text-[11px]">Seluruh model helm memiliki stok &ge; 5 unit.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('salesChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const labels = {!! json_encode($chart_labels) !!};
        const values = {!! json_encode($chart_values) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omzet',
                    data: values,
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    hoverBackgroundColor: '#2563eb',
                    borderRadius: 6,
                    maxBarThickness: 32,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, weight: 'bold', family: "'Inter', sans-serif" },
                        bodyFont: { size: 12, family: "'Inter', sans-serif" },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const val = context.raw || 0;
                                return 'Omzet: Rp ' + Number(val).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(226, 232, 240, 0.7)'
                        },
                        ticks: {
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#64748b',
                            callback: function(val) {
                                if (val >= 1000000) {
                                    return (val / 1000000).toFixed(0) + ' jt';
                                }
                                if (val >= 1000) {
                                    return (val / 1000).toFixed(0) + ' rb';
                                }
                                return val;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
