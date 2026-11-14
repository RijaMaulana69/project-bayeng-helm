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
    public function mount()
    {
        if (! Auth::guard('pelanggan')->check()) {
            return redirect()->route('pelanggan.login');
        }
    }

    #[Computed]
    public function carts()
    {
        if (! Auth::guard('pelanggan')->check()) {
            return collect();
        }

        return Cart::with(['helm', 'varian'])
            ->where('id_pelanggan', Auth::guard('pelanggan')->id())
            ->get();
    }

    #[Computed]
    public function cartCount()
    {
        return $this->carts->sum('qty');
    }

    #[Computed]
    public function cartTotal()
    {
        return $this->carts->sum(function ($item) {
            return $item->helm->harga_jual * $item->qty;
        });
    }

    public function addToCart($id_helm, $id_varian, $qty = 1)
    {
        if (! Auth::guard('pelanggan')->check()) {
            return redirect()->route('pelanggan.login');
        }

        $varian = VarianHelm::findOrFail($id_varian);

        if ($varian->stok < $qty) {
            session()->flash('error', 'Stok tidak mencukupi.');
            return;
        }

        $pelangganId = Auth::guard('pelanggan')->id();

        $cart = Cart::where('id_pelanggan', $pelangganId)
            ->where('id_helm', $id_helm)
            ->where('id_varian', $id_varian)
            ->first();

        if ($cart) {
            $newQty = $cart->qty + $qty;
            if ($newQty > $varian->stok) {
                session()->flash('error', 'Jumlah melebihi stok tersedia.');
                return;
            }
            $cart->update(['qty' => $newQty]);
        } else {
            Cart::create([
                'id_pelanggan' => $pelangganId,
                'id_helm'      => $id_helm,
                'id_varian'    => $id_varian,
                'qty'          => $qty,
            ]);
        }

        unset($this->carts, $this->cartCount, $this->cartTotal);

        $this->dispatch('cart-updated');

        session()->flash('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function removeFromCart($id_cart)
    {
        $cart = Cart::findOrFail($id_cart);

        if ($cart->id_pelanggan !== Auth::guard('pelanggan')->id()) {
            session()->flash('error', 'Akses tidak diizinkan.');
            return;
        }

        $cart->delete();

        unset($this->carts, $this->cartCount, $this->cartTotal);

        $this->dispatch('cart-updated');

        session()->flash('success', 'Produk dihapus dari keranjang.');
    }

    public function updateQty($id_cart, $qty)
    {
        $cart = Cart::findOrFail($id_cart);

        if ($cart->id_pelanggan !== Auth::guard('pelanggan')->id()) {
            session()->flash('error', 'Akses tidak diizinkan.');
            return;
        }

        $qty = max(1, (int) $qty);

        $varian = VarianHelm::findOrFail($cart->id_varian);

        if ($qty > $varian->stok) {
            session()->flash('error', "Stok tersedia hanya {$varian->stok} unit.");
            return;
        }

        $cart->update(['qty' => $qty]);

        unset($this->carts, $this->cartCount, $this->cartTotal);

        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.cart-manager');
    }
}
