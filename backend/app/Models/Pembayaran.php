<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'pembayaran_id';
    public $timestamps = false; // Custom timestamps

    protected $fillable = [
        'tagihan_id',
        'warga_id_pembayar',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_bayar',
        'bukti_bayar_path',
        'ocr_result_text',
        'status_verifikasi',
        'verifikasi_oleh_user_id',
        'verifikasi_timestamp',
        'catatan_verifikasi',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
        'verifikasi_timestamp' => 'datetime',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id', 'tagihan_id');
    }

    public function pembayar()
    {
        return $this->belongsTo(Warga::class, 'warga_id_pembayar', 'warga_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikasi_oleh_user_id', 'user_id');
    }
}
