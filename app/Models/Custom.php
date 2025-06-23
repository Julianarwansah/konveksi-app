<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Custom extends Model
{
    use HasFactory;

    protected $table = 'custom';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'pesanan_id',
        'customer_id',
        'template_id',
        'ukuran',
        'warna',
        'model',
        'jumlah',
        'harga_estimasi',
        'status',
        'estimasi_selesai',
        'tanggal_mulai',
        'catatan',
        'img',
    ];

    protected $casts = [
        'estimasi_selesai' => 'date',
        'tanggal_mulai' => 'date',
        'harga_estimasi' => 'decimal:2',
    ];

    // Status constants
    const STATUS_NOT_PRODUCED = 'Belum Diproduksi';
    const STATUS_IN_QUEUE = 'Dalam Antrian Produksi';
    const STATUS_IN_PRODUCTION = 'Dalam Produksi';
    const STATUS_PRODUCTION_COMPLETE = 'Selesai Produksi';

    public static function getStatuses()
    {
        return [
            'menunggu',
            'dalam_proses',
            'selesai',
            'dibatalkan'
        ];
    }

    // Relasi ke Pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Relasi ke Template
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    // Relasi ke PesananDetail
    public function pesananDetails()
    {
        return $this->hasMany(PesananDetail::class, 'custom_id');
    }

    // Relasi ke Antrian
    public function antrian()
    {
        return $this->hasOne(Antrian::class, 'custom_id');
    }

    // Accessor untuk status
    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    // Hitung total harga dari bahan
    public function calculateMaterialCost()
    {
        return $this->customDetails->sum('sub_total');
    }

    // Hitung total biaya tambahan
    public function calculateAdditionalCost()
    {
        return $this->customBiayas->sum('subtotal');
    }

    // Hitung total harga
    public function calculateTotalCost()
    {
        return $this->calculateMaterialCost() + $this->calculateAdditionalCost();
    }

    // Cek apakah bisa diproduksi
    public function canBeProduced()
    {
        return $this->status === self::STATUS_NOT_PRODUCED || 
               $this->status === self::STATUS_IN_QUEUE;
    }

    // Cek apakah dalam produksi
    public function isInProduction()
    {
        return $this->status === self::STATUS_IN_PRODUCTION;
    }

    // Cek apakah selesai diproduksi
    public function isProductionComplete()
    {
        return $this->status === self::STATUS_PRODUCTION_COMPLETE;
    }

    public function getStatusColor()
    {
        switch ($this->status) {
            case 'menunggu': return 'secondary';
            case 'dalam_proses': return 'primary';
            case 'selesai': return 'success';
            case 'dibatalkan': return 'danger';
            default: return 'light';
        }
    }
}