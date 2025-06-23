<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    use HasFactory;

    protected $table = 'pesanan_detail';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'custom_id',
        'jumlah',
        'ukuran',
        'warna',
        'harga',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function getSubTotalAttribute()
    {
        return $this->harga * $this->jumlah;
    }
    // Relasi ke tabel pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    // Relasi ke tabel produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    // Relasi ke tabel custom (pakaian custom)
    public function custom()
    {
        return $this->belongsTo(Custom::class, 'custom_id');
    }

    // Relasi ke tabel produk_ukuran melalui produk
    public function produkUkuran()
    {
        return $this->hasOneThrough(
            ProdukUkuran::class,
            Produk::class,
            'id', // Foreign key on Produk table
            'produk_id', // Foreign key on ProdukUkuran table
            'produk_id', // Local key on DetailPesanan table
            'id' // Local key on Produk table
        )->where('produk_ukuran.ukuran', $this->ukuran)
         ->where('produk_ukuran.warna_id', function($query) {
             $query->select('id')
                   ->from('produk_warna')
                   ->where('warna', $this->warna)
                   ->whereColumn('produk_id', 'produk_ukuran.produk_id');
         });
    }

    // Accessor untuk nama item
    public function getNamaItemAttribute()
    {
        if ($this->produk_id) {
            return $this->produk->nama;
        } elseif ($this->custom_id) {
            return 'Custom: ' . ($this->custom->model ?? 'Tanpa Model');
        }
        return 'Item Tidak Dikenali';
    }

    // Accessor untuk deskripsi item
    public function getDeskripsiItemAttribute()
    {
        $desc = [];
        
        if ($this->ukuran) {
            $desc[] = 'Ukuran: ' . $this->ukuran;
        }
        
        if ($this->warna) {
            $desc[] = 'Warna: ' . $this->warna;
        }
        
        if ($this->jumlah) {
            $desc[] = 'Jumlah: ' . $this->jumlah;
        }
        
        return implode(', ', $desc);
    }

    protected static function booted()
    {
        
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Modifikasi kondisi validasi di sini
            if ($model->custom_id && !in_array($model->pesanan->status_pembayaran, ['DP', 'Lunas', 'Menunggu Verifikasi'])) {
                throw new \Exception('Custom item hanya bisa ditambahkan setelah pembayaran DP, Lunas, atau Menunggu Verifikasi'); // Ubah pesan error juga (opsional)
            }
        });
    }
    
}