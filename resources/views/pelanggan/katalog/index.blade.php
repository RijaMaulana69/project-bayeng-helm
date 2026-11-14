@extends('layouts.pelanggan')
@section('title', 'Katalog Helm Resmi - Bayeng Putra Kuningan')

@section('content')
<div class="bg-slate-50 min-h-screen">
    {{-- Livewire Reactive Katalog Filter (Full Sticky Pill App Experience) --}}
    <livewire:katalog-filter />
</div>
@endsection
