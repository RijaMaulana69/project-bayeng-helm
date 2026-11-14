@extends('layouts.pelanggan')
@section('title', 'Keranjang Belanja - Bayeng Putra Helm')

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-6 pb-24 sm:pb-12">
    <div class="max-w-6xl mx-auto px-3 sm:px-5">
        {{-- Livewire Cart Manager Component --}}
        <livewire:cart-manager />
    </div>
</div>
@endsection
