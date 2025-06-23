<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $table = 'cart';

    protected $fillable = [
        'customer_id',
        'produk_id',
        'warna_id',
        'ukuran_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->harga_satuan * $this->jumlah;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Relasi ke Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    // Relasi ke ProdukWarna
    public function warna(): BelongsTo
    {
        return $this->belongsTo(ProdukWarna::class, 'warna_id');
    }

    // Relasi ke ProdukUkuran
    public function ukuran(): BelongsTo
    {
        return $this->belongsTo(ProdukUkuran::class, 'ukuran_id');
    }
}
