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
        // Tabel Pemasukan
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sumber'); // contoh: penjualan luar, sewa alat, dll
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });

        // Tabel Pengeluaran
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kategori'); // contoh: gaji, operasional, pembelian bahan
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });

        // Tabel Keuangan (rekap harian)
        Schema::create('keuangan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->decimal('total_pemasukan', 15, 2)->default(0);
            $table->decimal('total_pengeluaran', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0); // pemasukan - pengeluaran
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan');
        Schema::dropIfExists('pengeluaran');
        Schema::dropIfExists('pemasukan');
    }
};
