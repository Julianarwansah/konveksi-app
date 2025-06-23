<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $table = 'customer';
    protected $fillable = [
        'nama',
        'email',
        'password',
        'alamat',
        'no_telp',
        'img',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'customer_id');
    }
}