<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Penting untuk Sanctum API

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role_id',
        'username',
        'email',
        'password_hash',
        'fcm_token',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function warga()
    {
        return $this->hasOne(Warga::class, 'user_id', 'user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id_penerima', 'user_id');
    }

    public function pembayaranDiverifikasi()
    {
        return $this->hasMany(Pembayaran::class, 'verifikasi_oleh_user_id', 'user_id');
    }
}
