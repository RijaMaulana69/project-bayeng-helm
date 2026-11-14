<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Helm;
use App\Models\VarianHelm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class CartManager extends Component
{
    public array $selectedItems = [];

    public function mount()
    {
        if (! Auth::guard('pelanggan')->check()) {
            return redirect()->route('pelanggan.login');
        }

        $userCarts = Cart::where('id_pelanggan', Auth::guard('pelanggan')->id())->get();

        $selectedIds = $userCarts->where('selected', 1)->pluck('id_cart')->map(fn($id) => (int) $id)->all();

        // If none is selected or newly visited, select all by default
        if (empty($selectedIds) && $userCarts->isNotEmpty()) {
            Cart::where('id_pelanggan', Auth::guard('pelanggan')->id())->update(['selected' => 1]);
            $this->selectedItems = $userCarts->pluck('id_cart')->map(fn($id) => (int) $id)->all();
        } else {
            $this->selectedItems = $selectedIds;
        }
    }

    #[Computed]
    public function carts()
    {
        if (! Auth::guard('pelanggan')->check()) {
            return collect();
        }

        return Cart::with(['helm.merk', 'varian'])
            ->where('id_pelanggan', Auth::guard('pelanggan')->id())
            ->get();
    }

    #[Computed]
    public function selectedCarts()
    {
        return $this->carts->filter(function ($item) {
            return in_array((int) $item->id_cart, $this->selectedItems);
        });
    }

    #[Computed]
    public function selectedCount(): int
    {
        return $this->selectedCarts->sum('qty');
    }

    #[Computed]
    public function selectedTotal(): int
    {
        return $this->selectedCarts->sum(function ($item) {
            return ($item->helm->harga_jual ?? 0) * $item->qty;
        });
    }

    #[Computed]
    public function isAllSelected(): bool
    {
        if ($this->carts->isEmpty()) {
            return false;
        }
        return count($this->selectedItems) === $this->carts->count();
    }

    public function toggleSelectAll()
    {
        $allIds = $this->carts->pluck('id_cart')->map(fn($id) => (int) $id)->all();

        if (count($this->selectedItems) === count($allIds)) {
            $this->selectedItems = [];
            Cart::where('id_pelanggan', Auth::guard('pelanggan')->id())->update(['selected' => 0]);
        } else {
            $this->selectedItems = $allIds;
            Cart::where('id_pelanggan', Auth::guard('pelanggan')->id())->update(['selected' => 1]);
        }

        unset($this->selectedCarts, $this->selectedCount, $this->selectedTotal);
    }

    public function toggleSelectItem($id_cart)
    {
        $id_cart = (int) $id_cart;

        if (in_array($id_cart, $this->selectedItems)) {
            $this->selectedItems = array_values(array_diff($this->selectedItems, [$id_cart]));
            Cart::where('id_cart', $id_cart)->update(['selected' => 0]);
        } else {
            $this->selectedItems[] = $id_cart;
            Cart::where('id_cart', $id_cart)->update(['selected' => 1]);
        }

        unset($this->selectedCarts, $this->selectedCount, $this->selectedTotal);
    }

    public function incrementQty($id_cart)
    {
        $cart = Cart::with('varian')->findOrFail($id_cart);
        if ($cart->id_pelanggan !== Auth::guard('pelanggan')->id()) {
            return;
        }

        $maxStock = $cart->varian ? $cart->varian->stok : 999;
        if ($cart->qty < $maxStock) {
            $cart->increment('qty');
            unset($this->carts, $this->selectedCarts, $this->selectedCount, $this->selectedTotal);
            $this->dispatch('cart-updated');
        } else {
            session()->flash('warning', "Maksimal pembelian varian ini adalah {$maxStock} unit.");
        }
    }

    public function decrementQty($id_cart)
    {
        $cart = Cart::findOrFail($id_cart);
        if ($cart->id_pelanggan !== Auth::guard('pelanggan')->id()) {
            return;
        }

        if ($cart->qty > 1) {
            $cart->decrement('qty');
            unset($this->carts, $this->selectedCarts, $this->selectedCount, $this->selectedTotal);
            $this->dispatch('cart-updated');
        }
    }

    public function removeFromCart($id_cart)
    {
        $cart = Cart::findOrFail($id_cart);
        if ($cart->id_pelanggan !== Auth::guard('pelanggan')->id()) {
            return;
        }

        $cart->delete();
        $this->selectedItems = array_values(array_diff($this->selectedItems, [(int) $id_cart]));

        unset($this->carts, $this->selectedCarts, $this->selectedCount, $this->selectedTotal);
        $this->dispatch('cart-updated');
        session()->flash('success', 'Helm dihapus dari keranjang.');
    }

    public function removeSelected()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        Cart::where('id_pelanggan', Auth::guard('pelanggan')->id())
            ->whereIn('id_cart', $this->selectedItems)
            ->delete();

        $this->selectedItems = [];
        unset($this->carts, $this->selectedCarts, $this->selectedCount, $this->selectedTotal);
        $this->dispatch('cart-updated');
        session()->flash('success', 'Helm yang dipilih berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.cart-manager');
    }
}
