<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    use HasFactory;

    protected $table = 'keuangan';

    protected $fillable = [
        'tanggal',
        'total_pemasukan',
        'total_pengeluaran',
        'saldo',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_pemasukan' => 'decimal:2',
        'total_pengeluaran' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];
}
