<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'produk_id';

    protected $fillable = [
        'toko_id',
        'jenis_id',
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'foto_produk_path',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id', 'toko_id');
    }

    public function jenisSayur()
    {
        return $this->belongsTo(JenisSayur::class, 'jenis_id', 'jenis_id');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'produk_id', 'produk_id');
    }

    public function review()
    {
        return $this->hasMany(ReviewProduk::class, 'produk_id', 'produk_id');
    }
}
