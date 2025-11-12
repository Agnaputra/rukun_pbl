<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    protected $primaryKey = 'tagihan_id';
    const UPDATED_AT = null; // Hanya created_at

    protected $fillable = [
        'keluarga_id',
        'jenis_iuran_id',
        'bulan',
        'tahun',
        'nominal',
        'status_pembayaran',
        'jatuh_tempo',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'jatuh_tempo' => 'date',
    ];

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_id', 'keluarga_id');
    }

    public function jenisIuran()
    {
        return $this->belongsTo(JenisIuran::class, 'jenis_iuran_id', 'jenis_iuran_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id', 'tagihan_id');
    }
}
