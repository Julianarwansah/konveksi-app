<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'pembayaran_id',
        'total_harga',
        'sisa_pembayaran',
        'status',
        'status_pembayaran',
        'tanggal_selesai',
    ];

    // Relasi ke tabel customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Contoh perbaikan di model Pesanan.php
    public function setStatusPembayaranAttribute($value)
{
    // ... logika manipulasi ...
    $this->attributes['status_pembayaran'] = $value; // Atau nilai yang sudah dimanipulasi
}


    // Relasi ke tabel pembayaran (satu pembayaran saja)
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'pesanan_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // Relasi ke tabel detail_pesanan
    public function detailPesanan()
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }

    // Relasi ke tabel pakaian_custom (jika digunakan)
    public function custom()
    {
        return $this->hasMany(Custom::class, 'pesanan_id');
    }

    // Relasi ke pengiriman
    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'pesanan_id');
    }

    public function pesananDetails()
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }

    
}
