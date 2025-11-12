<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';
    protected $primaryKey = 'warga_id';




    protected $fillable = [
        'user_id',
        'keluarga_id',
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'telepon',
        'status_warga',
        'foto_ktp',
        'foto_wajah',
        'is_verified_face'
    ];

    protected $casts = [
        'is_verified_face' => 'boolean',
        'tanggal_lahir' => 'date',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_id', 'keluarga_id');
    }

    public function toko()
    {
        return $this->hasOne(Toko::class, 'warga_pemilik_id', 'warga_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'warga_pembeli_id', 'warga_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'warga_id_pembayar', 'warga_id');
    }

    public function review()
    {
        return $this->hasMany(ReviewProduk::class, 'warga_id', 'warga_id');
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenWarga::class, 'warga_id', 'warga_id');
    }
}
