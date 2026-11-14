<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Helm;
use App\Models\Cart;
use App\Models\VarianHelm;
use Illuminate\Support\Facades\Auth;

class AddToCartButton extends Component
{
    public Helm $helm;
    public $selectedVarian = null;
    public int $qty = 1;
    public $availableVariants = [];

    public function mount(Helm $helm)
    {
        $this->helm = $helm->load('varians');
        $this->availableVariants = $helm->varians->toArray();
    }

    public function selectVarian($id_varian)
    {
        $this->selectedVarian = $id_varian;
        $this->qty = 1;
    }

    public function incrementQty()
    {
        $maxStock = $this->getSelectedVariantStock();
        if ($this->qty < $maxStock) {
            $this->qty++;
        }
    }

    public function decrementQty()
    {
        if ($this->qty > 1) {
            $this->qty--;
        }
    }

    public function getSelectedVariantStock(): int
    {
        if (! $this->selectedVarian) {
            return 0;
        }

        $varian = VarianHelm::find($this->selectedVarian);
        return $varian ? $varian->stok : 0;
    }

    public function addToCart()
    {
        if (! Auth::guard('pelanggan')->check()) {
            return redirect()->route('pelanggan.login');
        }

        if (! $this->selectedVarian) {
            session()->flash('cart-error', 'Silakan pilih varian terlebih dahulu.');
            return;
        }

        $varian = VarianHelm::findOrFail($this->selectedVarian);

        if ($this->qty < 1 || $this->qty > $varian->stok) {
            session()->flash('cart-error', 'Jumlah tidak valid atau melebihi stok.');
            return;
        }

        $pelangganId = Auth::guard('pelanggan')->id();

        $cart = Cart::where('id_pelanggan', $pelangganId)
            ->where('id_helm', $this->helm->id_helm)
            ->where('id_varian', $this->selectedVarian)
            ->first();

        if ($cart) {
            $newQty = $cart->qty + $this->qty;
            if ($newQty > $varian->stok) {
                session()->flash('cart-error', "Stok tersedia hanya {$varian->stok} unit (sudah ada {$cart->qty} di keranjang).");
                return;
            }
            $cart->update(['qty' => $newQty]);
        } else {
            Cart::create([
                'id_pelanggan' => $pelangganId,
                'id_helm'      => $this->helm->id_helm,
                'id_varian'    => $this->selectedVarian,
                'qty'          => $this->qty,
            ]);
        }

        $this->dispatch('cart-updated');
        $this->dispatch('cart-added-success');

        session()->flash('cart-success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function getSelectedVariantProperty()
    {
        if (! $this->selectedVarian) {
            return null;
        }
        return collect($this->availableVariants)->firstWhere('id_varian', $this->selectedVarian);
    }

    public function render()
    {
        return view('livewire.add-to-cart-button', [
            'selectedVariantObj' => $this->selectedVariant,
        ]);
    }
}
