<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukBahan extends Model
{
    use HasFactory;
    protected $table = 'produk_bahan';
    protected $fillable = [
        'produk_id',
        'bahan_id',
        'jumlah',
        'harga',
        'sub_total',
    ];

    /**
     * Relasi ke model Produk.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Relasi ke model BahanBaku.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

}