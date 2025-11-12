<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use HasFactory;

    protected $table = 'toko';
    protected $primaryKey = 'toko_id';

    protected $fillable = [
        'warga_pemilik_id',
        'nama_toko',
        'deskripsi',
        'logo_path',
        'status_verifikasi',
    ];

    // --- RELASI ---

    public function pemilik()
    {
        return $this->belongsTo(Warga::class, 'warga_pemilik_id', 'warga_id');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'toko_id', 'toko_id');
    }
}
