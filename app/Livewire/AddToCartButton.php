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
    public $selectedWarna = null;
    public $selectedUkuran = null;
    public $selectedVarian = null;
    public int $qty = 1;
    public $availableVariants = [];
    public $availableColors = [];
    public $availableSizes = [];

    public function mount(Helm $helm)
    {
        $this->helm = $helm->load('varians');
        $this->availableVariants = $helm->varians->toArray();

        // Unique colors
        $this->availableColors = collect($this->availableVariants)
            ->pluck('warna')
            ->unique()
            ->filter()
            ->values()
            ->all();

        // Unique sizes with standard order (XS, S, M, L, XL, XXL, All Size)
        $standardSizeOrder = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'All Size'];
        $sizes = collect($this->availableVariants)
            ->pluck('ukuran')
            ->unique()
            ->filter()
            ->values()
            ->all();

        usort($sizes, function ($a, $b) use ($standardSizeOrder) {
            $posA = array_search($a, $standardSizeOrder);
            $posB = array_search($b, $standardSizeOrder);
            if ($posA !== false && $posB !== false) {
                return $posA <=> $posB;
            }
            if ($posA !== false) return -1;
            if ($posB !== false) return 1;
            return strcmp($a, $b);
        });
        $this->availableSizes = $sizes;
    }

    public function selectWarna($warna)
    {
        $this->selectedWarna = ($this->selectedWarna === $warna) ? null : $warna;
        $this->resolveSelectedVarian();
    }

    public function selectUkuran($ukuran)
    {
        $this->selectedUkuran = ($this->selectedUkuran === $ukuran) ? null : $ukuran;
        $this->resolveSelectedVarian();
    }

    public function resolveSelectedVarian()
    {
        if ($this->selectedWarna && $this->selectedUkuran) {
            $match = collect($this->availableVariants)->first(function ($v) {
                return $v['warna'] === $this->selectedWarna && $v['ukuran'] === $this->selectedUkuran;
            });

            if ($match) {
                $this->selectedVarian = $match['id_varian'];
                $this->qty = 1;
                return;
            }
        }

        $this->selectedVarian = null;
        $this->qty = 1;
    }

    public function isColorOutOfStock($color): bool
    {
        if ($this->selectedUkuran) {
            $match = collect($this->availableVariants)->first(function ($v) use ($color) {
                return $v['warna'] === $color && $v['ukuran'] === $this->selectedUkuran;
            });
            return ! $match || $match['stok'] <= 0;
        }

        $totalStock = collect($this->availableVariants)->where('warna', $color)->sum('stok');
        return $totalStock <= 0;
    }

    public function isSizeOutOfStock($size): bool
    {
        if ($this->selectedWarna) {
            $match = collect($this->availableVariants)->first(function ($v) use ($size) {
                return $v['warna'] === $this->selectedWarna && $v['ukuran'] === $size;
            });
            return ! $match || $match['stok'] <= 0;
        }

        $totalStock = collect($this->availableVariants)->where('ukuran', $size)->sum('stok');
        return $totalStock <= 0;
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
            if (! $this->selectedWarna && count($this->availableColors) > 0) {
                $this->dispatch('cart-error', ['message' => 'Pilih warna helm terlebih dahulu']);
                return;
            }
            if (! $this->selectedUkuran && count($this->availableSizes) > 0) {
                $this->dispatch('cart-error', ['message' => 'Pilih ukuran helm terlebih dahulu']);
                return;
            }
            $this->dispatch('cart-error', ['message' => 'Pilih warna dan ukuran terlebih dahulu']);
            return;
        }

        $varian = VarianHelm::findOrFail($this->selectedVarian);

        if ($this->qty < 1 || $this->qty > $varian->stok) {
            $this->dispatch('cart-error', ['message' => 'Stok helm tidak mencukupi']);
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
                $this->dispatch('cart-error', ['message' => "Stok tersisa {$varian->stok} unit (sudah ada {$cart->qty} di keranjang)"]);
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
        $this->dispatch('cart-added-success', ['message' => 'Berhasil ditambahkan ke keranjang']);
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
