<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rw extends Model
{
    use HasFactory;

    protected $table = 'rw';
    protected $primaryKey = 'rw_id';
    public $timestamps = false; // Sesuai SQL dump Anda

    protected $fillable = [
        'nomor_rw',
        'nama_ketua_rw',
        'alamat_sekretariat',
    ];

    /**
     * Relasi: Satu RW memiliki banyak RT.
     */
    public function rt()
    {
        return $this->hasMany(Rt::class, 'rw_id', 'rw_id');
    }

}
