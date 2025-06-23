<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukWarna extends Model
{
    use HasFactory;

    protected $table = 'produk_warna';

    protected $fillable = [
        'produk_id', // Foreign key ke tabel produk
        'warna',     // Nama warna
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function ukuran()
    {
        return $this->hasMany(ProdukUkuran::class, 'warna_id');
    }
}