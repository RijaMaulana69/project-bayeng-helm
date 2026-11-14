<div class="relative" @click.outside="$wire.closeDropdown()">
    {{-- Bell Button --}}
    <button type="button" 
            wire:click="toggleOpen" 
            class="relative inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl text-slate-700 hover:text-blue-600 hover:bg-slate-100/80 transition-all cursor-pointer"
            title="Notifikasi">
        <i class="fa-regular fa-bell text-base sm:text-lg"></i>
        @if($activeCount > 0)
            <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500 ring-2 ring-white"></span>
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    @if($isOpen)
        <div class="absolute right-0 sm:-right-8 mt-2 w-80 sm:w-84 max-w-[calc(100vw-2rem)] bg-white rounded-2xl border border-slate-200/90 shadow-xl z-50 overflow-hidden divide-y divide-slate-100 animate-scale-in">
            
            {{-- Header --}}
            <div class="px-4 py-3 bg-slate-50/60 flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-slate-900">Notifikasi</span>
                    @if($activeCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full bg-rose-100 text-rose-600 text-[10px] font-bold">
                            {{ $activeCount }} aktif
                        </span>
                    @endif
                </div>
                <a href="{{ route('pelanggan.pesanan') }}" class="text-[11px] font-medium text-blue-600 hover:text-blue-700">
                    Semua Pesanan
                </a>
            </div>

            {{-- Content List --}}
            <div class="max-h-72 overflow-y-auto divide-y divide-slate-100">
                @forelse($recentOrders as $order)
                    @php
                        $firstItem = $order->details->first();
                        $iconClass = match($order->status) {
                            'ready' => 'fa-store text-emerald-600 bg-emerald-50 border-emerald-200',
                            'unpaid_pickup' => 'fa-clock text-blue-600 bg-blue-50 border-blue-200',
                            'pending' => 'fa-wallet text-amber-600 bg-amber-50 border-amber-200',
                            'completed' => 'fa-check text-slate-600 bg-slate-100 border-slate-200',
                            default => 'fa-box text-sky-600 bg-sky-50 border-sky-200',
                        };
                    @endphp
                    <a href="{{ route('pelanggan.pesanan.detail', $order->id_pesanan) }}" 
                       class="p-3 sm:p-3.5 flex items-start gap-3 hover:bg-slate-50 transition-colors block">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs border shrink-0 {{ $iconClass }}">
                            <i class="fa-solid {{ strtok($iconClass, ' ') }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-[11px] font-mono font-medium text-slate-700">{{ $order->no_pesanan }}</span>
                                <span class="text-[10px] text-slate-400">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs font-medium text-slate-900 truncate mt-0.5">
                                {{ $firstItem ? $firstItem->nama_helm : 'Pesanan Helm' }}
                            </p>
                            <div class="flex items-center justify-between mt-1 text-[11px]">
                                <span class="font-medium text-blue-600">{{ $order->status_label }}</span>
                                @if(in_array($order->status, ['ready', 'unpaid_pickup']))
                                    <span class="font-mono font-bold text-slate-700">Kode: {{ $order->kode_pickup }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="py-8 px-4 text-center space-y-1.5">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-sm">
                            <i class="fa-regular fa-bell-slash"></i>
                        </div>
                        <p class="text-xs font-medium text-slate-800">Belum ada notifikasi</p>
                        <p class="text-[11px] text-slate-400">Pembaruan pesanan akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

            {{-- Footer --}}
            <div class="p-2.5 bg-white text-center">
                <a href="{{ route('pelanggan.pesanan') }}" 
                   class="text-xs font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                    <span>Buka Riwayat Pesanan</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

        </div>
    @endif
</div>
