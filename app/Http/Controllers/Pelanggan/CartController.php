<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index()
    {
        return view('pelanggan.cart.index');
    }
}
