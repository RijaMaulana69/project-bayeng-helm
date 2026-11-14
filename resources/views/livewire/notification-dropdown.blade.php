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

    {{-- Dropdown Panel (E-Commerce List Style) --}}
    @if($isOpen)
        <div class="absolute right-0 sm:-right-8 mt-2 w-80 sm:w-96 max-w-[calc(100vw-1.5rem)] bg-white rounded-2xl border border-slate-200 shadow-xl z-50 overflow-hidden divide-y divide-slate-100 animate-scale-in">
            
            {{-- Header --}}
            <div class="px-4 py-3 bg-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-900">Notifikasi</span>
                    @if($activeCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-semibold border border-blue-200/60">
                            {{ $activeCount }} pesanan aktif
                        </span>
                    @endif
                </div>
                <a href="{{ route('pelanggan.pesanan') }}" class="text-[11px] font-medium text-blue-600 hover:text-blue-700">
                    Lihat Semua
                </a>
            </div>

            {{-- Content List --}}
            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                @forelse($recentOrders as $order)
                    @php
                        $firstItem = $order->details->first();
                        $info = match($order->status) {
                            'ready' => [
                                'title' => 'Pesanan Siap Diambil',
                                'desc' => ($firstItem ? $firstItem->nama_helm : 'Helm') . ' sudah siap. Tunjukkan Kode: ' . $order->kode_pickup,
                            ],
                            'unpaid_pickup' => [
                                'title' => 'Menunggu Diambil di Gerai',
                                'desc' => ($firstItem ? $firstItem->nama_helm : 'Helm') . ' • Kode Pickup: ' . $order->kode_pickup,
                            ],
                            'pending' => [
                                'title' => 'Menunggu Pembayaran',
                                'desc' => 'Selesaikan pembayaran ' . format_rupiah($order->grand_total) . ' untuk pesanan #' . $order->no_pesanan,
                            ],
                            'paid' => [
                                'title' => 'Pembayaran Dikonfirmasi',
                                'desc' => 'Pembayaran diterima. Helm ' . ($firstItem ? $firstItem->nama_helm : '') . ' segera disiapkan staf.',
                            ],
                            'processing' => [
                                'title' => 'Pesanan Sedang Disiapkan',
                                'desc' => 'Staf toko sedang menyiapkan & memeriksa helm pesananmu.',
                            ],
                            'completed' => [
                                'title' => 'Pesanan Selesai',
                                'desc' => 'Helm telah diterima di gerai. Terima kasih telah berbelanja!',
                            ],
                            'cancelled' => [
                                'title' => 'Pesanan Dibatalkan',
                                'desc' => 'Pesanan #' . $order->no_pesanan . ' telah dibatalkan.',
                            ],
                            default => [
                                'title' => 'Status Pesanan',
                                'desc' => 'Pesanan #' . $order->no_pesanan . ' status: ' . $order->status_label,
                            ],
                        };

                        $isActiveOrder = in_array($order->status, ['ready', 'unpaid_pickup', 'pending', 'processing']);
                    @endphp
                    <a href="{{ route('pelanggan.pesanan.detail', $order->id_pesanan) }}" 
                       class="p-3 sm:p-3.5 flex items-start gap-3 hover:bg-slate-50 transition-colors block {{ $isActiveOrder ? 'bg-blue-50/20' : '' }}">
                        
                        {{-- Product Thumbnail --}}
                        <div class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                            @if($firstItem && $firstItem->helm && $firstItem->helm->gambar_url)
                                <img src="{{ $firstItem->helm->gambar_url }}" alt="{{ $firstItem->nama_helm }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-helmet-safety text-slate-400 text-sm"></i>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1.5">
                                <h4 class="text-xs font-semibold text-slate-900 truncate">
                                    {{ $info['title'] }}
                                </h4>
                                <span class="text-[10px] text-slate-400 shrink-0">
                                    {{ $order->created_at->diffForHumans(null, true) }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-600 leading-snug line-clamp-2 mt-0.5">
                                {{ $info['desc'] }}
                            </p>
                        </div>

                        {{-- Unread Dot Indicator --}}
                        @if($isActiveOrder)
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1.5 shrink-0" title="Pesanan Aktif"></div>
                        @endif
                    </a>
                @empty
                    <div class="py-10 px-4 text-center space-y-2">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-sm">
                            <i class="fa-regular fa-bell"></i>
                        </div>
                        <p class="text-xs font-medium text-slate-900">Belum ada notifikasi</p>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Update status pesanan helm akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    @endif
</div>
