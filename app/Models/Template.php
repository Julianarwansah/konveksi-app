<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Template extends Model
{
    
    use HasFactory;

    protected $table = 'template';

    protected $fillable = [
        'model',
        'deskripsi',
        'kategori',
        'harga_estimasi',
    ];

    // Template memiliki banyak detail bahan
    public function details()
    {
        return $this->hasMany(TemplateDetail::class, 'template_id');
    }

    // Template memiliki banyak gambar
    public function gambar()
    {
        return $this->hasMany(TemplateGambar::class, 'template_id');
    }

    public function custom()
    {
        return $this->hasMany(Custom::class);
    }

    // Template memiliki banyak warna
    public function warna()
    {
        return $this->hasMany(TemplateWarna::class, 'template_id');
    }

    public function warnas()
    {
        return $this->hasMany(TemplateWarna::class);
    }

    // Relasi ke pakaian custom
    public function pakaianCustom()
    {
        return $this->hasMany(Custom::class, 'model', 'model');
    }

    // Scope untuk filter kategori
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}