<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahan';
    protected $fillable = [
        'nama',
        'satuan',
        'stok',
        'harga',
        'img'
    ];

    protected $casts = [
        'stok' => 'decimal:2',
        'harga' => 'decimal:2'
    ];

    protected $attributes = [
        'stok' => 0,
    ];

    // Relasi ke tabel produk_bahan
    public function produkBahan()
    {
        return $this->hasMany(ProdukBahan::class, 'bahan_id');
    }

    // Relasi ke tabel template_detail
    public function templateDetail()
    {
        return $this->hasMany(TemplateDetail::class, 'bahan_id');
    }

}