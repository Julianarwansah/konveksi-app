<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukUkuran extends Model
{
    use HasFactory;
    protected $table = 'produk_ukuran';
    protected $fillable = [
        'produk_id',
        'warna_id',
        'ukuran',
        'stok',
    ];
    
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    // Relasi ke model ProdukWarnas
    public function warna()
    {
        return $this->belongsTo(ProdukWarna::class, 'warna_id');
    }

    // Relasi ke tabel detail_pesanan
    public function detailPesanan()
    {
        return $this->hasMany(PesananDetail::class, 'produk_ukuran_id');
    }
}