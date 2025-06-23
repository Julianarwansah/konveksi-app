<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProdukGambarDetail;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama',
        'kategori',
        'harga',
        'total_stok',
        'deskripsi',
        'img',
    ];

    // Relasi ke tabel detail_pesanan
    public function detailPesanan()
    {
        return $this->hasMany(PesananDetail::class, 'produk_id');
    }

    public function pesananDetail()
    {
        return $this->hasMany(PesananDetail::class);
    }

    public function gambarDetails()
    {
        return $this->hasMany(ProdukGambar::class, 'produk_id');
    }

    // Relasi ke tabel produk_bahan_baku_detail
    public function bahanBakuDetails()
    {
        return $this->hasMany(ProdukBahan::class, 'produk_id');
    }
    // Relasi ke tabel produk_ukuran
    public function ukuran()
    {
        return $this->hasMany(ProdukUkuran::class, 'produk_id');
    }

     // Relasi dengan UkuranProduk
     public function ukuranProduk()
     {
         return $this->hasMany(ProdukUkuran::class);
     }

     // Relasi dengan UkuranProduk
     public function produkUkuran()
     {
         return $this->hasMany(ProdukUkuran::class);
     }
     
     // Relasi ke tabel produk_warna
    public function warna()
    {
        return $this->hasMany(ProdukWarna::class, 'produk_id');
    }

    public function produkBahan()
    {
        return $this->hasMany(ProdukBahan::class, 'produk_id');
    }

    public function produkWarna()
{
    return $this->hasMany(ProdukWarna::class, 'produk_id');
}


}