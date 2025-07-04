# Konveksi-App

Sebuah aplikasi Laravel fullstack untuk manajemen konveksi, baik produk jadi maupun produk custom.

## Deskripsi Proyek

Aplikasi ini dirancang untuk mengelola bisnis konveksi dengan fitur:
- Penjualan produk jadi (e-commerce)
- Pemesanan produk custom
- Manajemen produksi
- Manajemen inventori
- Laporan keuangan

## Fitur Utama

### Umum
- ✅ Sistem multi-aktor dengan hak akses berbeda
- ✅ Manajemen produk jadi dan custom
- ✅ Pembayaran online & offline
- ✅ Notifikasi sistem

### Khusus Per Aktor
1. **Admin E-commerce**
   - Kelola katalog produk
   - Kelola promosi & diskon
   - Kelola ulasan produk

2. **Kasir**
   - Transaksi penjualan offline
   - Pembayaran & invoice
   - Retur produk

3. **Manager**
   - Laporan penjualan & keuangan
   - Analisis bisnis
   - Manajemen stok

4. **Produksi**
   - Tracking pesanan custom
   - Jadwal produksi
   - Update status produksi

5. **Customer**
   - Beli produk jadi
   - Pesan produk custom
   - Tracking pesanan
   - Riwayat transaksi

## Teknologi

**Frontend:**
- Laravel Blade
- Bootstrap 5
- JavaScript
- jQuery (opsional)

**Backend:**
- Laravel 10
- MySQL

**Lainnya:**
- [Tambahkan library/packages lain yang digunakan]

## Instalasi

1. Clone repositori:
   ```bash
   git clone https://github.com/[username-anda]/konveksi-app.git
   cd konveksi-app
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Buat file `.env`:
   ```bash
   cp .env.example .env
   ```

4. Generate key:
   ```bash
   php artisan key:generate
   ```

5. Konfigurasi database di `.env`:
   ```env
   DB_DATABASE=nama_database
   DB_USERNAME=username
   DB_PASSWORD=password
   ```

6. Migrasi database:
   ```bash
   php artisan migrate --seed
   ```

7. Jalankan aplikasi:
   ```bash
   php artisan serve
   ```

## Akses Demo

**Admin E-commerce:**
- Email: julinarwansahh@gmail.com
- Password: 123456789

**Kasir:**
- Email: kasir@konveksi.com
- Password: 123456789

**Manager:**
- Email: manager@konveksi.com
- Password: 123456789

**Produksi:**
- Email: produksi@konveksi.com
- Password: 123456789

**Customer:**
- Daftar manual atau gunakan:
- Email: customer@example.com
- Password: 123456789

## databases mysql phpmyadmin

- databases ada di dalam ( database/akonveksisimple.sql )

---

Dikembangkan dengan ❤ oleh Julian Arwansah
