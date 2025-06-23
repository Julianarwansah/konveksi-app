<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayaran';

    protected $fillable = [
        'pesanan_id',
        'jumlah',
        'metode',
        'status',
        'bukti_bayar',
        'tanggal_bayar',
        'catatan',
        'is_dp'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
        'is_dp' => 'boolean'
    ];

    protected $attributes = [
        'status' => 'Menunggu Konfirmasi',
        'is_dp' => false
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
    
}