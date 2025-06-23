<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateDetail extends Model
{
    use HasFactory;

    protected $table = 'template_detail';

    protected $fillable = [
        'template_id',
        'bahan_id',
        'jumlah',
        'harga',
        'subtotal',
    ];

    // Relasi ke Template
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    // Relasi ke Bahan
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
