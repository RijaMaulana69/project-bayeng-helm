@extends('layouts.pelanggan')

@section('title', 'Katalog Helm')

@section('content')
<div class="container py-5">
    {{-- Header --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold mb-2" style="color: var(--dark);">Katalog Helm</h1>
        <p class="mb-0" style="color: var(--muted); font-size: 1.1rem;">
            Temukan helm impian Anda
        </p>
    </div>

    {{-- Livewire Katalog Filter --}}
    <livewire:katalog-filter />
</div>
@endsection
