<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class CartCounter extends Component
{
    public int $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    #[On('cart-updated')]
    public function updateCount()
    {
        if (Auth::guard('pelanggan')->check()) {
            $this->count = Cart::where('id_pelanggan', Auth::guard('pelanggan')->id())->sum('qty');
        }
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
