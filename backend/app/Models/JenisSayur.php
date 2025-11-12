<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSayur extends Model
{
    use HasFactory;

    protected $table = 'jenis_sayur';
    protected $primaryKey = 'jenis_id';
    public $timestamps = false; // Sesuai SQL dump

    protected $fillable = [
        'nama_jenis',
        'deskripsi',
    ];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'jenis_id', 'jenis_id');
    }
}
