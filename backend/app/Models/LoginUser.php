<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class LoginUser extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'login_user';

    protected $fillable = [
        'username',
        'password',
        'nama_petugas',
        'kelas_id',
        'sekolah_id', // 👈 Tambahkan ini agar bisa di-insert saat register
        'jenjang_id', // 👈 Tambahkan ini juga
    ];

    /**
     * Relasi ke data Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Kelas::class, 'kelas_id');
    }

    /**
     * 💡 Tambahkan relasi ke data Sekolah
     * Pastikan namespace model Sekolah kamu sudah benar (misal: \App\Models\Sekolah)
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Sekolah::class, 'sekolah_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'nama_petugas' => $this->nama_petugas,
            'sekolah_id' => $this->sekolah_id,
            'kelas_id' => $this->kelas_id,
        ];
    }
}