<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    use HasFactory;

    protected $table = 'rt';
    protected $primaryKey = 'rt_id';
    public $timestamps = false; // Sesuai SQL dump Anda

    protected $fillable = [
        'rw_id',
        'nomor_rt',
        'nama_ketua_rt',
    ];

    /**
     * Relasi: Satu RT dimiliki oleh satu RW.
     */
    public function rw()
    {
        return $this->belongsTo(Rw::class, 'rw_id', 'rw_id');
    }

    /**
     * Relasi: Satu RT memiliki banyak Keluarga.
     */
    public function keluarga()
    {
        return $this->hasMany(Keluarga::class, 'rt_id', 'rt_id');
    }
}
