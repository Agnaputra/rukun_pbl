<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenWarga extends Model
{
    use HasFactory;

    protected $table = 'dokumen_warga';
    protected $primaryKey = 'dokumen_id';
    const UPDATED_AT = null; // Hanya created_at

    protected $fillable = [
        'warga_id',
        'nama_dokumen',
        'file_path',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id', 'warga_id');
    }
}
