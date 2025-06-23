<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateWarna extends Model
{
    use HasFactory;

    protected $table = 'template_warna';

    protected $fillable = [
        'template_id',
        'warna',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}
