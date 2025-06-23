<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';

    protected $fillable = [
        'user_id',
        'sumber',
        'jumlah',
        'keterangan',
        'tanggal',
    ];

    /**
     * Relasi ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
