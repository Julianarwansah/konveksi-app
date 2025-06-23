<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Roles Table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->timestamps();
        });

        // Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles');
            $table->string('nama', 100);
            $table->string('email', 100);
            $table->string('password');
            $table->string('img')->nullable();
            $table->timestamps();
        });

        // Customer Table
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('email', 100);
            $table->string('password');
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('img')->nullable();
            $table->timestamps();
        });

        // Bahan Table
        Schema::create('bahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('satuan', 20);
            $table->decimal('stok', 10, 2)->default(0.00);
            $table->decimal('harga', 10, 2);
            $table->string('img')->nullable();
            $table->timestamps();
        });

        // Produk Table
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kategori', 50);
            $table->boolean('is_custom')->default(false);
            $table->decimal('harga', 10, 2);
            $table->integer('total_stok')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('img')->nullable();
            $table->timestamps();
        });

        // Warna Table
        Schema::create('produk_warna', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk');
            $table->string('warna', 255);
            $table->timestamps();
        });

        // Ukuran Table
        Schema::create('produk_ukuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk');
            $table->foreignId('warna_id')->constrained('produk_warna');
            $table->string('ukuran', 10);
            $table->integer('stok')->default(0);
            $table->timestamps();
        });

        // Produk Gambar Table
        Schema::create('produk_gambar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk');
            $table->string('gambar', 255);
            $table->timestamps();
        });

        // Produk Bahan Table
        Schema::create('produk_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk');
            $table->foreignId('bahan_id')->constrained('bahan');
            $table->decimal('jumlah', 10, 2);
            $table->decimal('harga', 15, 2);
            $table->decimal('sub_total', 15, 2)->storedAs('jumlah * harga');
            $table->timestamps();
        });

        // Template Table
        Schema::create('template', function (Blueprint $table) {
            $table->id();
            $table->string('model', 255);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_estimasi', 10, 2)->nullable();
            $table->timestamps();
        });

        // Template Detail Table
        Schema::create('template_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('template');
            $table->foreignId('bahan_id')->constrained('bahan');
            $table->decimal('jumlah', 10, 2);
            $table->decimal('harga', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // Template Gambar Table
        Schema::create('template_gambar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('template');
            $table->string('gambar', 255);
            $table->timestamps();
        });

        // Template Warna Table
        Schema::create('template_warna', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('template');
            $table->string('warna', 255);
            $table->timestamps();
        });

        // Custom Table (pakaian_custom)
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('pembayaran_id')->nullable()->constrained('pembayaran');
            
            $table->decimal('total_harga', 10, 2);
            $table->decimal('sisa_pembayaran', 10, 2)->default(0);
            
            $table->enum('status', [
                // ----- Alur pembayaran & verifikasi -----
                'Menunggu Pembayaran',       // 1. order baru
                'Menunggu Konfirmasi',       // 2. bukti bayar di-upload
                'Pembayaran Diverifikasi',   // 3. bayar valid
                
                // ----- Alur custom/manufaktur -----
                'Dalam Antrian Produksi',    // 4a. antrean produksi
                'Dalam Produksi',            // 5a. proses produksi berjalan
                'Selesai Produksi',          // 6a. produksi selesai
                
                // ----- Alur pengemasan & pengiriman (produk jadi atau setelah produksi) -----
                'Sedang Pengemasan',         // 4b/7. kemas paket
                'Siap Dikirim',              // 5b/8. paket siap pickup
                'Dalam Pengiriman',          // 6b/9. paket dalam perjalanan
                'Selesai Pengiriman',        // 7b/10. paket diterima
                
                // ----- Status akhir umum -----
                'Selesai'                    // 11. order closed
            ])->default('Menunggu Pembayaran');
            
            $table->enum('status_pembayaran', [
                'Menunggu Verifikasi',
                'DP',
                'Lunas',
                'Pembayaran Gagal'
            ])->default('Menunggu Verifikasi');
            
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });
        
        Schema::create('custom', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pesanan_id')->constrained('pesanan');
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('template_id')->nullable()->constrained('template');

            $table->string('ukuran', 10);
            $table->string('warna', 50)->nullable();
            $table->string('model', 100)->nullable();

            $table->integer('jumlah')->default(1);
            $table->decimal('harga_estimasi', 10, 2)->nullable();

            // Update enum status sesuai alur manufaktur
            $table->enum('status', [
                'Dalam Antrian Produksi',  // 4a. antrean produksi
                'Dalam Produksi',          // 5a. proses produksi berjalan
                'Selesai Produksi'         // 6a. produksi selesai
            ])->default('Dalam Antrian Produksi');

            $table->date('estimasi_selesai')->nullable();
            $table->date('tanggal_mulai')->nullable();

            $table->text('catatan')->nullable();
            $table->string('img', 255)->nullable();

            $table->timestamps();
        });

        // Pesanan Detail Table
        Schema::create('pesanan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan');
            $table->foreignId('produk_id')->nullable()->constrained('produk');
            $table->foreignId('custom_id')->nullable()->constrained('custom');
            $table->integer('jumlah');
            $table->string('ukuran', 255)->nullable();
            $table->string('warna', 255)->nullable();
            $table->decimal('harga', 10, 2);
            $table->decimal('sub_total', 10, 2)->storedAs('jumlah * harga');
            $table->timestamps();
        });

        // Pembayaran Table
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan');
            $table->decimal('jumlah', 10, 2);
            $table->enum('metode', ['Transfer Bank', 'COD', 'Kartu Kredit', 'Lainnya']);
            $table->enum('status', [
                'Menunggu Konfirmasi', 
                'Berhasil', 
                'Gagal'
            ])->default('Menunggu Konfirmasi');
            $table->string('bukti_bayar')->nullable();
            $table->dateTime('tanggal_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('is_dp')->default(false);
            $table->timestamps();
        });

        // Pengiriman Table
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->text('alamat');
            $table->string('kurir', 100);
            $table->string('resi', 100)->nullable();
            $table->string('foto_resi', 255)->nullable();
            $table->decimal('biaya', 10, 2);
            $table->enum('status', [
                'Dalam Pengiriman', 
                'Selesai Pengiriman'
            ])->default('Dalam Pengiriman');
            $table->timestamps();
        });

        // Antrian Table
        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_id')->constrained('custom');
            $table->date('tanggal');
            $table->integer('jumlah')->default(1);  // <-- kolom jumlah produk yang dipesan
            $table->enum('status', [
                'Dalam Antrian Produksi',
                'Dalam Produksi',
                'Selesai Produksi'
            ])->default('Dalam Antrian Produksi');
            $table->timestamps();

            $table->index(['tanggal', 'urutan']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraints
        Schema::dropIfExists('antrian');
        Schema::dropIfExists('kapasitas');
        Schema::dropIfExists('pengiriman');
        Schema::dropIfExists('custom_biaya');
        Schema::dropIfExists('custom_detail');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('pesanan_detail');
        Schema::dropIfExists('custom');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('template_warna');
        Schema::dropIfExists('template_gambar');
        Schema::dropIfExists('template_detail');
        Schema::dropIfExists('template');
        Schema::dropIfExists('produk_bahan');
        Schema::dropIfExists('produk_gambar');
        Schema::dropIfExists('ukuran');
        Schema::dropIfExists('warna');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('biaya');
        Schema::dropIfExists('bahan');
        Schema::dropIfExists('customer');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};