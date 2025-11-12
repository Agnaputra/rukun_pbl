<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'notifikasi_id';
    const UPDATED_AT = null; // Hanya created_at

    protected $fillable = [
        'user_id_penerima',
        'judul',
        'isi_pesan',
        'tipe_notifikasi',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_penerima', 'user_id');
    }
}
