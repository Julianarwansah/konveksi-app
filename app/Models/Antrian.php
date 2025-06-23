<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrian';

    protected $fillable = [
        'custom_id',
        'tanggal',
        'jumlah',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected $attributes = [
        'status' => self::STATUS_DALAM_ANTRIAN_PRODUKSI,
    ];

    // Status constantsclass Antrian extends Model

    const STATUS_DALAM_ANTRIAN_PRODUKSI = 1;
    const STATUS_DALAM_PRODUKSI = 2;
    const STATUS_SELESAI_PRODUKSI = 3;

    public static function getStatusOptions()
    {
        return [
            self::STATUS_DALAM_ANTRIAN_PRODUKSI => 'Dalam Antrian Produksi',
            self::STATUS_DALAM_PRODUKSI => 'Dalam Produksi',
            self::STATUS_SELESAI_PRODUKSI => 'Selesai Produksi'
        ];
    }

    public static function getCustomStatusMapping()
    {
        return [
            self::STATUS_DALAM_ANTRIAN_PRODUKSI => Custom::STATUS_IN_QUEUE,
            self::STATUS_DALAM_PRODUKSI => Custom::STATUS_IN_PRODUCTION,
            self::STATUS_SELESAI_PRODUKSI => Custom::STATUS_PRODUCTION_COMPLETE
        ];
    }


    /**
     * Relasi ke model Custom
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function custom()
    {
        return $this->belongsTo(Custom::class, 'custom_id');
    }

    // App\Models\Antrian.php
    public function getStatusTextAttribute()
    {
        $statuses = [
            1 => 'Dalam Antrian Produksi',
            2 => 'Dalam Produksi',
            3 => 'Selesai Produksi'
        ];
        return $statuses[$this->status] ?? '-';
    }

    public function getStatusNameAttribute()
{
    // Pastikan status di-cast ke integer
    $status = (int)$this->attributes['status'];
    return self::getStatusOptions()[$status] ?? '-';
}

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    /**
     * Mutator untuk memastikan status valid
     */
    public function setStatusAttribute($value)
    {
        if (!array_key_exists($value, self::getStatusOptions())) {
            throw new \InvalidArgumentException("Status tidak valid: {$value}");
        }
        $this->attributes['status'] = $value;
    }
}