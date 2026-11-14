# Bayeng Putra Helm - E-Commerce Click & Collect (Store Pickup) 🛡️🏍️

Aplikasi web **E-Commerce dengan model Click & Collect (Ambil di Toko)** untuk toko helm **Bayeng Putra Helm**, dibangun menggunakan framework **Laravel 13**, **Livewire 3 (Reaktif tanpa reload)**, dan dirancang khusus dengan penerapan standar keamanan web modern untuk memenuhi ketentuan Tugas Besar mata kuliah **Cyber Security**.

---

## 📌 1. Deskripsi Aplikasi & Model Bisnis

Pada industri helm, pembeli seringkali ragu melakukan transaksi online secara konvensional (kirim via ekspedisi) karena kendala **kesesuaian ukuran lingkar kepala (M, L, XL)** dan tingginya biaya retur jika helm kekecilan/kebesaran.

Aplikasi **Bayeng Putra Helm** hadir dengan solusi **Click & Collect (Store Pick-up)**:
1. **Pelanggan** dapat mencari helm, melihat ketersediaan varian warna dan ukuran riil secara online, memasukkan ke keranjang secara interaktif, dan melakukan checkout pemesanan.
2. Pelanggan memperoleh **e-Tiket dengan Kode Pickup Unik** (misal: `PKP-A1B2C3`) serta batas waktu pengambilan di toko fisik.
3. Pelanggan datang ke toko fisik Bayeng Putra Helm untuk melakukan *fitting* langsung.
4. **Kasir Toko** melakukan verifikasi dan validasi kode pickup pada sistem, menyerahkan helm, dan sistem otomatis menyelesaikan pesanan (`completed`) serta mengurangi stok.
5. **Owner** memantau analitik omzet toko melalui grafik interaktif dan dapat mengekspor rekapitulasi laporan penjualan dalam format **PDF** dan **CSV**.

---

## 🚀 2. Fitur Utama Sistem

### 👤 Pelanggan (Publik & Member)
- **Landing Page Interaktif**: Banner promo, produk unggulan, dan panduan 4 langkah alur *Click & Collect*.
- **Katalog Livewire**: Pencarian helm instan (*debounced*), filter kategori, filter merk, filter rentang harga, dan sorting tanpa *reload* halaman.
- **Detail Helm & Stok Varian Real-Time**: Pemilihan kombinasi warna dan ukuran dinamis dengan notifikasi stok otomatis.
- **Cart Manager Reaktif**: Tambah item, ubah kuantitas (+/-), hapus item, dan hitung subtotal otomatis tanpa *refresh*.
- **Checkout & Kode Pickup**: Pilihan pembayaran (Transfer / QRIS / Bayar di Toko saat ambil) dan penerbitan tiket pengambilan.
- **Riwayat Pesanan & Upload Bukti**: Pelacakan status pesanan real-time, log pergerakan status, dan upload bukti bayar terproteksi.
- **Manajemen Profil**: Pengelolaan data diri dan pembaruan password dengan validasi keamanan.

### 👷 Karyawan (Kasir)
- **Dashboard Operasional**: Ringkasan produk, transaksi hari ini, dan pesanan siap ambil.
- **Validasi Pickup (Barcode/Kode Input)**: Input cepat kode pickup pelanggan untuk verifikasi data sebelum serah terima helm.
- **Manajemen Pesanan Masuk**: Konfirmasi status pesanan (`paid` ➔ `processing` ➔ `ready` ➔ `completed`).
- **Manajemen Inventaris**: Tambah produk helm, varian warna & ukuran, merk, kategori, serta pencatatan stok masuk.

### 👨‍💼 Owner (Pemilik)
- **Dashboard Analitik Eksekutif**: Metrik omzet harian & bulanan serta grafik tren penjualan (Chart.js).
- **Laporan Penjualan & Ekspor**:
  - Rekap penjualan bulanan & tahunan.
  - **Ekspor PDF** resmi berformat kop toko (DomPDF).
  - **Ekspor CSV/Excel** untuk analisis data di spreadsheet.
- **Kelola Karyawan**: Kontrol akun kasir dan monitoring waktu aktivitas terakhir.
- **Audit Trail (Activity Log)**: Catatan riwayat seluruh aktivitas penting sistem secara kronologis (login, logout, transaksi, perubahan status, IP address, user-agent).
- **Pengaturan Profil Toko**: Konfigurasi identitas toko, nomor kontak, dan alamat toko.

---

## 🛡️ 3. Penerapan Keamanan (Cyber Security)

Sistem ini menerapkan prinsip *Defense in Depth* dan *Secure Software Development Life Cycle (SSDLC)*:

| Komponen Keamanan | Implementasi pada Sistem |
| :--- | :--- |
| **Password Hashing** | Menggunakan algoritma **Bcrypt** dengan cost factor 12 (bukan plaintext atau md5). |
| **Validasi Password Kuat** | Registrasi mewajibkan minimal 8 karakter, kombinasi huruf besar, huruf kecil, dan angka. |
| **Rate Limiting (Anti Brute-Force)** | Diterapkan pada endpoint login Pelanggan dan Admin (maksimal 5 percobaan per menit per IP/akun). |
| **CSRF Protection** | Seluruh form dan request mutasi dilindungi token CSRF otomatis dari Laravel. |
| **SQL Injection Prevention** | Menggunakan **Eloquent ORM & PDO Prepared Statements** berparameter penuh. |
| **XSS Prevention** | Auto-escaping pada template engine Blade (`{{ $var }}`) untuk mencegah script injection. |
| **Pencegahan IDOR** | Pembatasan akses pesanan berdasarkan relasi `id_pelanggan` pada session yang aktif. |
| **File Upload Hardening** | Validasi MIME type biner via `finfo` (hanya gambar JPG/PNG) + proteksi `.htaccess` anti eksekusi PHP di folder `uploads/`. |
| **HTTP Security Headers** | Header `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection`, dan `Referrer-Policy`. |
| **Audit Trail (Activity Log)** | Pencatatan otomatis riwayat aksi, waktu, IP address, dan identitas user ke database. |

---

## 🧪 4. Hasil Self-Testing Keamanan (5 Skenario Uji)

| No | Skenario Uji | Cara Pengujian | Hasil Sebelum Perbaikan | Tindakan Perbaikan | Hasil Akhir |
| :-: | :--- | :--- | :--- | :--- | :-: |
| **1** | **SQL Injection (SQLi)** | Injeksi payload `' OR '1'='1' --` pada form pencarian katalog & login. | Query berpotensi rentan jika query dibuat string concat. | Menggunakan PDO Prepared Statements & Eloquent ORM berparameter. | **LOLOS (Aman)** |
| **2** | **Cross-Site Scripting (XSS)** | Injeksi script `<script>alert('XSS')</script>` pada ulasan, nama, dan catatan checkout. | Script berisiko tereksekusi pada browser pengguna lain jika tidak disanitasi. | Output dirender via Blade Escaping Engine (`{{ }}`) yang otomatis menjalankan `htmlspecialchars`. | **LOLOS (Aman)** |
| **3** | **Broken Access Control & IDOR** | Mengganti nomor ID pesanan di URL (`/pelanggan/pesanan/{id}`) menggunakan akun pelanggan lain. | Data pesanan dan kode pickup orang lain berpotensi bocor. | Menambahkan validasi kepemilikan (`$pesanan->id_pelanggan === auth()->id()`), jika tidak cocok melempar status `HTTP 403 Forbidden`. | **LOLOS (Aman)** |
| **4** | **Brute Force Login Attack** | Melakukan percobaan login berkali-kali secara beruntun dengan password salah. | Akun rentan diserang metode tebak password secara otomatis. | Mengaktifkan **RateLimiter** Laravel (maksimal 5 kali salah per menit, diblokir sementara). | **LOLOS (Aman)** |
| **5** | **Unrestricted File Upload** | Mengunggah script berbahaya berkedok file bukti bayar (`shell.php.jpg` atau `.php`). | Server dapat dieksploitasi untuk Remote Code Execution (RCE). | Validasi ekstensi ketat + inspeksi biner MIME type via PHP `finfo` + konfigurasi `.htaccess` larangan eksekusi PHP di folder uploads. | **LOLOS (Aman)** |

---

## 🤖 5. Penggunaan AI Agent

Sesuai instruksi tugas poin 3e, proyek ini dikembangkan secara transparan dengan bantuan AI Agent:

- **Alat AI yang Digunakan**: Antigravity AI Coding Agent (Model: Claude 3.5 / Hybrid AI).
- **Bagian yang Dibantu**:
  1. *Refactoring & Architecture*: Perancangan ulang dari arsitektur PHP native ke framework Laravel dengan pola Multi-Guard Auth.
  2. *Komponen Reaktif*: Pembuatan Livewire components untuk Live Search katalog dan Shopping Cart tanpa reload.
  3. *Security Hardening*: Penyusunan skrip proteksi rate limiting, HTTP security headers, dan audit activity log.
  4. *Laporan & Dokumentasi*: Pembuatan template cetak PDF (DomPDF) dan penyusunan berkas pengujian keamanan.
- **Contoh Prompt Utama**:
  - *"Migrasikan database toko helm ke Laravel Eloquent dengan sistem autentikasi multi-guard (Pelanggan via email, Admin via username) serta integrasikan rate limiter untuk mencegah serangan brute force."*
  - *"Buat komponen Livewire untuk pencarian helm dan filter kategori secara instan tanpa reload halaman."*
- **Metode Verifikasi Mahasiswa**: Seluruh kode yang dihasilkan ditinjau secara manual, diuji fungsionalitasnya di Laragon, diuji integritas database-nya, serta divalidasi keamanannya dengan skenario pengujian mandiri.

---

## 🎓 6. Potensi Pengembangan ke Skripsi (Tingkat 4)

- **Usulan Judul Skripsi**:
  > *"Pengembangan Sistem E-Commerce dengan Model Click-and-Collect Menggunakan Verifikasi Transaksi Aman (Secure Token Verification) dan Role-Based Access Control pada Toko Bayeng Putra Helm"*
- **Rumusan Masalah**:
  1. Bagaimana merancang mekanisme reservasi stok produk (*Click & Collect*) yang aman dari pembatalan sepihak dan *race condition* stok?
  2. Bagaimana menerapkan verifikasi kode pickup berbasis token terenkripsi/QR dinamis untuk mencegah pemalsuan bukti pengambilan barang?
  3. Bagaimana mengimplementasikan Audit Trail berkinerja tinggi guna meningkatkan akuntabilitas transaksi pada sistem e-commerce UMKM?
- **Fitur Lanjutan untuk Skripsi**:
  - Integrasi QR Code dinamis berbasis Time-based One-Time Password (TOTP) untuk tiket pengambilan.
  - Notifikasi real-time via WhatsApp Gateway (Fonnte/Twilio) saat helm siap diambil di toko.
  - Rekomendasi ukuran helm pintar berbasis pengukuran lingkar kepala pembeli.

---

## 🛠️ 7. Tech Stack & Kebutuhan Sistem

- **Framework**: Laravel 13 (PHP >= 8.3)
- **Reactivity**: Livewire 4 + Alpine.js
- **Database**: MySQL / MariaDB
- **Reporting Engine**: Barryvdh Laravel DomPDF
- **Frontend & UI**: Bootstrap 5.3, Font Awesome 6, Chart.js, SweetAlert2, DataTables
- **Web Server**: Apache / Nginx (Laragon)

---

## 📦 8. Panduan Instalasi Lokal (Laragon / XAMPP)

1. **Clone / Letakkan Proyek:**
   Letakkan folder proyek di direktori `D:\laragon\www\project_bayeng_helm`.

2. **Setup Konfigurasi Environment:**
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Pastikan pengaturan database di `.env` sudah sesuai:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=project_bayeng_helm
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Install Dependensi Composer:**
   ```bash
   composer install
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Import Database:**
   Buat database baru bernama `project_bayeng_helm` di phpMyAdmin, lalu import data SQL yang tersedia.

6. **Akses Aplikasi:**
   - **Frontend Pelanggan**: `http://localhost/project_bayeng_helm/public/`
   - **Katalog Helm**: `http://localhost/project_bayeng_helm/public/katalog`
   - **Portal Admin & Kasir**: `http://localhost/project_bayeng_helm/public/admin/login`

---

## 🔑 9. Akun Demo Uji Coba

Untuk keperluan demonstrasi dan pengujian oleh Dosen Penguji:

| Peran | Username / Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Owner (Pemilik)** | `owner` | `owner123` | Akses penuh dashboard, kelola karyawan, ekspor laporan penjualan PDF/CSV, audit log, dan pengaturan. |
| **Karyawan (Kasir)** | `karyawan` | `karyawan123` | Akses menu validasi pickup, kelola pesanan masuk, dan manajemen produk/stok. |
| **Pelanggan (Demo)** | `budi@gmail.com` | `password` | Akses belanja, checkout ambil di toko, e-tiket pickup, dan riwayat pesanan. |

---

## 🌐 10. URL Aplikasi & Repositori

- **URL Aplikasi Publik**: *(Akan diisi setelah deployment hosting publik)*
- **URL Git Repository**: *(Akan diisi setelah push ke GitHub)*
