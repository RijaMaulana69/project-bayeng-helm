<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\Helm;
use App\Models\LogStatusPesanan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\VarianHelm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealistisTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $karyawan = User::where('id_role', 2)->first() ?? User::where('username', 'karyawan')->first();
        $cashierUsername = $karyawan ? $karyawan->username : 'karyawan';

        // 35 Nama & No. HP Pelanggan Indonesia yang realistis
        $customersPool = [
            ['nama' => 'Hendra Wijaya', 'telepon' => '081289123456'],
            ['nama' => 'Dimas Pratama', 'telepon' => '085712345678'],
            ['nama' => 'Siti Rahmawati', 'telepon' => '081398765432'],
            ['nama' => 'Rizky Kurniawan', 'telepon' => '087811223344'],
            ['nama' => 'Dian Permata', 'telepon' => '081299887766'],
            ['nama' => 'Aditya Putra', 'telepon' => '085677889900'],
            ['nama' => 'Maya Indah', 'telepon' => '081344556677'],
            ['nama' => 'Eko Prasetyo', 'telepon' => '087788990011'],
            ['nama' => 'Bagus Santoso', 'telepon' => '081233445566'],
            ['nama' => 'Rina Wulandari', 'telepon' => '085811223344'],
            ['nama' => 'Fauzan Hakim', 'telepon' => '081322334455'],
            ['nama' => 'Wahyu Hidayat', 'telepon' => '087855667788'],
            ['nama' => 'Dewi Lestari', 'telepon' => '081266778899'],
            ['nama' => 'Andi Firmansyah', 'telepon' => '085799001122'],
            ['nama' => 'Arif Rahman', 'telepon' => '081377889900'],
            ['nama' => 'Agus Setiawan', 'telepon' => '087711223344'],
            ['nama' => 'Ilham Saputra', 'telepon' => '081255667788'],
            ['nama' => 'Nanda Putri', 'telepon' => '085622334455'],
            ['nama' => 'Kevin Sanjaya', 'telepon' => '081388990011'],
            ['nama' => 'Bayu Pratama', 'telepon' => '087844556677'],
            ['nama' => 'Surya Darma', 'telepon' => '081244332211'],
            ['nama' => 'Anisa Fitri', 'telepon' => '085733445566'],
            ['nama' => 'Doni Kusuma', 'telepon' => '081366778899'],
            ['nama' => 'Farah Diba', 'telepon' => '087899001122'],
            ['nama' => 'Gilang Ramadhan', 'telepon' => '085611223344'],
            ['nama' => 'Hesti Utami', 'telepon' => '081277889900'],
            ['nama' => 'Indra Lesmana', 'telepon' => '087733445566'],
            ['nama' => 'Joko Susilo', 'telepon' => '081355667788'],
            ['nama' => 'Kartika Sari', 'telepon' => '085866778899'],
            ['nama' => 'Lukman Hakim', 'telepon' => '087822334455'],
            ['nama' => 'Mega Silvia', 'telepon' => '081288990011'],
            ['nama' => 'Nurul Huda', 'telepon' => '085744556677'],
            ['nama' => 'Budi Santoso', 'telepon' => '081234567890'],
            ['nama' => 'Rangga Pratama', 'telepon' => '081312349876'],
            ['nama' => 'Taufik Hidayat', 'telepon' => '085788776655'],
        ];

        // Daftar Helm populer untuk transaksi toko
        $helms = Helm::with('varians')->get();
        if ($helms->isEmpty()) {
            return;
        }

        $metodeList = ['tunai', 'tunai', 'qris', 'transfer', 'ewallet'];

        DB::transaction(function () use ($customersPool, $helms, $metodeList, $cashierUsername) {
            $pesanans = Pesanan::orderBy('id_pesanan', 'desc')->get();
            $custCount = count($customersPool);
            $helmCount = $helms->count();

            foreach ($pesanans as $idx => $pesanan) {
                // Pilih pelanggan realistis secara siklis
                $cust = $customersPool[$idx % $custCount];

                // Update data pesanan
                $pesanan->penerima = $cust['nama'];
                $pesanan->telepon  = $cust['telepon'];

                // Update juga model Pelanggan terkait jika ada
                if ($pesanan->pelanggan) {
                    $pesanan->pelanggan->update([
                        'nama'    => $cust['nama'],
                        'telepon' => $cust['telepon'],
                    ]);
                }

                // Rotasi metode pembayaran realistis
                $pesanan->metode_bayar = $metodeList[$idx % count($metodeList)];
                if ($pesanan->metode_bayar === 'tunai') {
                    $pesanan->metode_bayar_detail = 'bayar_di_toko';
                } elseif ($pesanan->metode_bayar === 'transfer') {
                    $pesanan->metode_bayar_detail = 'bca';
                } elseif ($pesanan->metode_bayar === 'qris') {
                    $pesanan->metode_bayar_detail = 'qris_dinamis';
                } else {
                    $pesanan->metode_bayar_detail = 'gopay';
                }

                // Pilih helm dan varian yang berbeda
                $chosenHelm = $helms[($idx * 3) % $helmCount];
                $varians = $chosenHelm->varians;
                $chosenVarian = $varians->isNotEmpty() 
                    ? $varians[$idx % $varians->count()] 
                    : null;

                $warna = $chosenVarian ? $chosenVarian->warna : 'Black';
                $ukuran = $chosenVarian ? $chosenVarian->ukuran : 'L';
                $harga = (float) $chosenHelm->harga_jual;
                $qty = 1;
                $subtotal = $harga * $qty;

                // Update atau buat ulang rincian pesanan
                $detail = $pesanan->details->first();
                if ($detail) {
                    $detail->update([
                        'id_helm'   => $chosenHelm->id_helm,
                        'id_varian' => $chosenVarian ? $chosenVarian->id_varian : $detail->id_varian,
                        'nama_helm' => $chosenHelm->nama_helm,
                        'warna'     => $warna,
                        'ukuran'    => $ukuran,
                        'harga'     => $harga,
                        'qty'       => $qty,
                        'subtotal'  => $subtotal,
                    ]);
                } else {
                    DetailPesanan::create([
                        'id_pesanan' => $pesanan->id_pesanan,
                        'id_helm'    => $chosenHelm->id_helm,
                        'id_varian'  => $chosenVarian ? $chosenVarian->id_varian : null,
                        'nama_helm'  => $chosenHelm->nama_helm,
                        'warna'      => $warna,
                        'ukuran'     => $ukuran,
                        'harga'      => $harga,
                        'qty'        => $qty,
                        'subtotal'   => $subtotal,
                    ]);
                }

                $pesanan->total_belanja = $subtotal;
                $pesanan->grand_total   = $subtotal;
                $pesanan->save();

                // Pastikan ada log yang tercatat oleh staf kasir (Ari Akbar)
                $hasCashierLog = $pesanan->logs->contains(function ($l) use ($cashierUsername) {
                    return $l->diubah_oleh === $cashierUsername;
                });

                if (!$hasCashierLog) {
                    LogStatusPesanan::create([
                        'id_pesanan'     => $pesanan->id_pesanan,
                        'status_dari'    => 'pending',
                        'status_ke'      => $pesanan->status,
                        'catatan'        => 'Dilayani dan diverifikasi oleh staf kasir gerai',
                        'diubah_oleh'    => $cashierUsername,
                    ]);
                }
            }
        });
    }
}
