<div class="relative" @click.outside="$wire.closeDropdown()">
    {{-- Bell Button --}}
    <button type="button" 
            wire:click="toggleOpen" 
            class="relative inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl transition-all cursor-pointer {{ $isOpen ? 'text-blue-600 bg-blue-50/80' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-100/80' }}"
            title="Notifikasi">
        <i class="{{ $isOpen ? 'fa-solid' : 'fa-regular' }} fa-bell text-base sm:text-lg"></i>
        @if($activeCount > 0)
            <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500 ring-2 ring-white"></span>
            </span>
        @endif
    </button>

    @if($isOpen)
        {{-- Mobile Backdrop Overlay --}}
        <div class="fixed inset-0 bg-slate-900/30 backdrop-blur-2xs z-40 sm:hidden" 
             wire:click="closeDropdown"></div>

        {{-- Dropdown Panel (Mobile-First Sheet & Desktop Floating Dropdown) --}}
        <div class="fixed inset-x-3 top-16 mt-1.5 sm:mt-2 sm:absolute sm:inset-auto sm:top-full sm:right-0 w-auto sm:w-96 max-w-lg sm:max-w-none bg-white rounded-2xl border border-slate-200 shadow-2xl sm:shadow-xl z-50 overflow-hidden divide-y divide-slate-100 animate-scale-in">
            
            {{-- Header --}}
            <div class="px-4 py-3 bg-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs sm:text-sm font-bold text-slate-900">Notifikasi</span>
                    @if($activeCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-100">
                            {{ $activeCount }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('pelanggan.pesanan') }}" 
                       @click="$wire.closeDropdown()"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                        Lihat Semua
                    </a>
                    {{-- Mobile Dismiss Button --}}
                    <button type="button" 
                            wire:click="closeDropdown" 
                            class="sm:hidden w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:text-slate-800 transition-colors cursor-pointer"
                            title="Tutup">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- Content List --}}
            <div class="max-h-[62vh] sm:max-h-80 overflow-y-auto divide-y divide-slate-100 overscroll-contain">
                @forelse($recentOrders as $order)
                    @php
                        $firstItem = $order->details->first();
                        $otherCount = $order->details->count() - 1;

                        $orderDate = $order->created_at;
                        if ($orderDate->isToday()) {
                            $timeString = 'Hari ini, ' . $orderDate->format('H:i');
                        } elseif ($orderDate->isYesterday()) {
                            $timeString = 'Kemarin, ' . $orderDate->format('H:i');
                        } else {
                            $timeString = $orderDate->format('d M, H:i');
                        }

                        $statusTitle = match($order->status) {
                            'ready'         => 'Pesanan Siap Diambil',
                            'unpaid_pickup' => 'Menunggu Diambil',
                            'pending'       => 'Menunggu Pembayaran',
                            'paid'          => 'Pembayaran Diterima',
                            'processing'    => 'Pesanan Diproses',
                            'completed'     => 'Pesanan Selesai',
                            'cancelled'     => 'Pesanan Dibatalkan',
                            default         => 'Status: ' . $order->status_label,
                        };

                        $statusTitleColor = match($order->status) {
                            'ready'              => 'text-emerald-600',
                            'unpaid_pickup'      => 'text-blue-600',
                            'pending'            => 'text-amber-600',
                            'paid', 'processing' => 'text-sky-600',
                            'completed'          => 'text-slate-800',
                            'cancelled'          => 'text-rose-600',
                            default              => 'text-slate-700',
                        };
                    @endphp

                    <a href="{{ route('pelanggan.pesanan.detail', $order->id_pesanan) }}" 
                       class="p-3 sm:p-3.5 flex items-start gap-3 hover:bg-slate-50/80 active:bg-slate-100 transition-colors block">
                        
                        {{-- Thumbnail Foto Helm --}}
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                            @if($firstItem && $firstItem->helm && $firstItem->helm->gambar_url)
                                <img src="{{ $firstItem->helm->gambar_url }}" alt="{{ $firstItem->nama_helm }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-helmet-safety text-slate-400 text-sm"></i>
                            @endif
                        </div>

                        {{-- Info Transaksi --}}
                        <div class="flex-1 min-w-0">
                            {{-- Baris 1: Status & Waktu --}}
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold {{ $statusTitleColor }} truncate">
                                    {{ $statusTitle }}
                                </span>
                                <span class="text-[10px] text-slate-400 shrink-0 font-normal">
                                    {{ $timeString }}
                                </span>
                            </div>

                            {{-- Baris 2: Nama Helm & Varian --}}
                            <p class="text-xs text-slate-900 font-medium truncate mt-0.5">
                                {{ $firstItem ? $firstItem->nama_helm : 'Pesanan Helm' }}
                                @if($firstItem && ($firstItem->warna || $firstItem->ukuran))
                                    <span class="text-slate-400 font-normal text-[11px]">
                                        ({{ $firstItem->warna ?? '' }}{{ $firstItem->warna && $firstItem->ukuran ? ', ' : '' }}{{ $firstItem->ukuran ?? '' }})
                                    </span>
                                @endif
                                @if($otherCount > 0)
                                    <span class="text-slate-400 font-normal text-[11px]">+{{ $otherCount }} lainnya</span>
                                @endif
                            </p>

                            {{-- Baris 3: Info Kunci (Kode Pickup / Nominal) --}}
                            <div class="text-[11px] text-slate-500 mt-1 flex items-center justify-between">
                                @if(in_array($order->status, ['ready', 'unpaid_pickup']))
                                    <span>Kode Pickup: <span class="font-mono font-bold text-blue-600 tracking-wide text-xs">{{ $order->kode_pickup }}</span></span>
                                @elseif($order->status === 'pending')
                                    <span>Total: <span class="font-semibold text-slate-900">{{ format_rupiah($order->grand_total) }}</span></span>
                                @elseif(in_array($order->status, ['paid', 'processing']))
                                    <span>Ambil di Gerai Kuningan</span>
                                @elseif($order->status === 'completed')
                                    <span class="text-slate-400">Selesai diambil di gerai</span>
                                @else
                                    <span class="font-mono text-slate-400">{{ $order->no_pesanan }}</span>
                                @endif

                                <span class="text-[10px] text-blue-600 font-semibold flex items-center gap-0.5">
                                    <span>Detail</span>
                                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="py-10 px-4 text-center space-y-2">
                        <div class="w-11 h-11 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-base">
                            <i class="fa-regular fa-bell"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-900">Belum ada notifikasi</p>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Update status pesanan helm kamu akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    @endif
</div>
