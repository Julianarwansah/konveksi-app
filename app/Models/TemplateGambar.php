<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateGambar extends Model
{
    use HasFactory;

    protected $table = 'template_gambar';

    protected $fillable = [
        'template_id',
        'gambar',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}
