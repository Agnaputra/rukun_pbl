<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'keluarga';
    protected $primaryKey = 'keluarga_id';

    // Hanya 'created_at'.
    const UPDATED_AT = null;

    protected $fillable = [
        'rt_id',
        'nomor_kk',
        'alamat',
        'kepala_keluarga_id',
        'file_kk_scan',
    ];

    // --- RELASI ---

    public function rt()
    {
        return $this->belongsTo(Rt::class, 'rt_id', 'rt_id');
    }

    public function warga()
    {
        return $this->hasMany(Warga::class, 'keluarga_id', 'keluarga_id');
    }

    public function kepalaKeluarga()
    {
        return $this->belongsTo(Warga::class, 'kepala_keluarga_id', 'warga_id');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'keluarga_id', 'keluarga_id');
    }
}
