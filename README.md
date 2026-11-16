# Bayeng Putra Helm

Aplikasi web e-commerce untuk gerai **Bayeng Putra Helm** (Kuningan, Jawa Barat) dengan model transaksi **Click & Collect (Pesan Online, Ambil di Toko)**. Sistem ini memadukan katalog produk online, reservasi stok dengan kode pickup unik, layanan konsultasi chat kasir secara real-time, dan panel operasional kasir serta owner.

---

## Gambaran Proyek

Kendala utama pembelian helm secara daring konvensional adalah risiko salah ukuran (M, L, XL) dan proses retur yang memakan waktu serta biaya. 

Sistem ini menyelesaikan kendala tersebut dengan alur **Click & Collect**:
1. Pelanggan memilih model, warna, dan ukuran helm secara online melalui katalog.
2. Saat checkout, sistem mengunci stok dan menerbitkan e-tiket dengan **Kode Pickup Unik** (misal: `PKP-A1B2C3`).
3. Pelanggan datang ke toko fisik Bayeng Putra Helm untuk mencoba (*fitting*) langsung helm yang telah disiapkan.
4. Kasir memvalidasi kode pickup pada sistem, mencocokkan pesanan, dan menyelesaikan transaksi.

---

## Fitur Utama

### 1. Sisi Pelanggan (Storefront)
- **Katalog Real-Time**: Pencarian instan, filter kategori, merk, rentang harga, dan sorting tanpa muat ulang halaman (Livewire).
- **Detail Varian & Stok**: Pengecekan stok aktif berdasarkan kombinasi warna dan ukuran helm.
- **Keranjang Belanja Reaktif**: Update kuantitas dan kalkulasi subtotal instan.
- **Checkout & Kode Pickup**: Pilihan metode bayar (Transfer, QRIS, atau Bayar Langsung di Kasir) serta penerbitan batas waktu pengambilan.
- **Riwayat Pesanan**: Pelacakan status pesanan, e-tiket pickup, dan upload bukti bayar.
- **Live Chat Toko**: Widget obrolan langsung dengan kasir untuk tanya jawab ketersediaan stok, rekomendasi ukuran, dan panduan lokasi toko.

### 2. Sisi Kasir (Karyawan)
- **Validasi Kode Pickup**: Verifikasi cepat tiket pengambilan pelanggan sebelum helm diserahkan.
- **Manajemen Pesanan Masuk**: Konfirmasi pembayaran, proses pesanan, hingga status selesai.
- **Panel Chat Kasir**: Antarmuka obrolan pelanggan (master-detail responsif mobile/desktop), tombol balas cepat (*quick replies*), dan ringkasan produk terkait.
- **Inventaris Helm**: Input produk baru, manajemen varian warna dan ukuran, serta pencatatan stok masuk.

### 3. Sisi Owner (Pemilik)
- **Dashboard Analitik**: Ringkasan omzet harian, transaksi aktif, dan grafik tren penjualan.
- **Laporan & Rekapitulasi**: Ekspor laporan penjualan ke format **PDF** (berkop resmi toko) dan format **CSV** untuk olah data di spreadsheet.
- **Manajemen Karyawan**: Kontrol akun kasir dan riwayat aktivitas.
- **Audit Log (Riwayat Aktivitas)**: Pencatatan kronologis aktivitas krusial sistem (login, transaksi, perubahan status, IP address).
- **Pengaturan Profil Toko**: Konfigurasi identitas gerai, kontak, dan alamat operasional.

---

## Teknologi yang Digunakan

- **Backend**: Laravel 11 / 12, PHP 8.2+
- **Reactivity & Frontend**: Livewire 3, Alpine.js, Tailwind CSS, Blade Template Engine
- **Database**: MySQL 8.0+
- **Laporan**: Barryvdh Laravel DomPDF
- **Aset & Build Tool**: Vite, Font Awesome 6

---

## Panduan Instalasi Lokal

### 1. Kebutuhan Sistem
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `gd`, `fileinfo`)
- Composer
- Node.js & npm
- MySQL / MariaDB (misal: Laragon atau XAMPP)

### 2. Langkah Pemasangan

1. **Clone repositori**:
   ```bash
   git clone https://github.com/RijaMaulana69/project-bayeng-helm.git
   cd project-bayeng-helm
   ```

2. **Install dependensi PHP & JavaScript**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   Salin file `.env.example` ke `.env`:
   ```bash
   cp .env.example .env
   ```
   Sesuaikan konfigurasi koneksi database di file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=project_bayeng_helm
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Import Database**:
   Buat database baru bernama `project_bayeng_helm` di MySQL/phpMyAdmin, lalu import berkas `project_bayeng_helm.sql`.

6. **Build Aset Frontend**:
   ```bash
   npm run build
   ```

7. **Jalankan Aplikasi**:
   Jika menggunakan server lokal bawaan Laravel:
   ```bash
   php artisan serve
   ```
   Atau buka melalui host Laragon / vhost Apache lokal Anda.

---

## Akun Demo Pengujian

| Peran | Username / Email | Password | Akses Utama |
| :--- | :--- | :--- | :--- |
| **Owner** | `owner` | `owner123` | Dashboard omzet, ekspor laporan PDF/CSV, audit log, kelola karyawan. |
| **Karyawan** | `karyawan` | `karyawan123` | Validasi pickup, kelola pesanan, panel chat pelanggan, stok produk. |
| **Pelanggan** | `budi@gmail.com` | `Password123` | Belanja katalog, checkout ambil di toko, e-tiket pickup, riwayat pesanan. |
