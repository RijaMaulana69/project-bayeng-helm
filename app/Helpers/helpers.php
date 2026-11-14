<?php

use Illuminate\Support\Number;

/**
 * Format angka ke Rupiah
 */
if (!function_exists('format_rupiah')) {
    function format_rupiah(float|int|string|null $angka): string
    {
        return 'Rp ' . number_format((float)($angka ?? 0), 0, ',', '.');
    }
}

/**
 * Generate Nomor Pesanan Otomatis
 */
if (!function_exists('generate_order_number')) {
    function generate_order_number(): string
    {
        $date = date('Ymd');
        $last = \App\Models\Pesanan::where('no_pesanan', 'like', "ORD-{$date}%")
            ->orderBy('no_pesanan', 'desc')
            ->value('no_pesanan');

        if ($last) {
            $lastNum = (int) substr($last, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return "ORD-{$date}-{$nextNum}";
    }
}

/**
 * Generate Kode Pickup Unik (6 karakter alfanumerik)
 */
if (!function_exists('generate_pickup_code')) {
    function generate_pickup_code(): string
    {
        do {
            $code = 'PKP-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        } while (\App\Models\Pesanan::where('kode_pickup', $code)->whereIn('status', ['pending', 'unpaid_pickup', 'paid', 'processing', 'ready'])->exists());

        return $code;
    }
}
