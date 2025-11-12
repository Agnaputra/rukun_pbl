<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $primaryKey = 'pesanan_id';
    public $timestamps = false; // Menggunakan 'tanggal_pesan'

    protected $fillable = [
        'warga_pembeli_id',
        'tanggal_pesan',
        'total_harga',
        'status_pesanan',
        'alamat_pengiriman',
    ];

    protected $casts = [
        'tanggal_pesan' => 'datetime',
        'total_harga' => 'decimal:2',
    ];

    public function pembeli()
    {
        return $this->belongsTo(Warga::class, 'warga_pembeli_id', 'warga_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id', 'pesanan_id');
    }
}
