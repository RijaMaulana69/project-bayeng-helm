<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Pesanan;

class NotificationDropdown extends Component
{
    public bool $isOpen = false;

    public function toggleOpen()
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function closeDropdown()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        $pelanggan = Auth::guard('pelanggan')->user();

        if (! $pelanggan) {
            return <<<'BLADE'
            <div></div>
            BLADE;
        }

        $activeCount = Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->whereIn('status', ['pending', 'unpaid_pickup', 'paid', 'processing', 'ready'])
            ->count();

        $recentOrders = $this->isOpen
            ? Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->with(['details.helm'])
                ->latest('id_pesanan')
                ->take(4)
                ->get()
            : collect();

        return view('livewire.notification-dropdown', [
            'activeCount' => $activeCount,
            'recentOrders' => $recentOrders,
        ]);
    }
}
