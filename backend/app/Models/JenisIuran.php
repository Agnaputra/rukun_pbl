<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisIuran extends Model
{
    use HasFactory;

    protected $table = 'jenis_iuran';
    protected $primaryKey = 'jenis_iuran_id';
    public $timestamps = false;

    protected $fillable = [
        'nama_iuran',
        'deskripsi',
        'nominal_default',
        'periode',
    ];

    protected $casts = [
        'nominal_default' => 'decimal:2',
    ];

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'jenis_iuran_id', 'jenis_iuran_id');
    }
}
