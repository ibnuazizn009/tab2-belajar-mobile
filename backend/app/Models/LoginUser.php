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
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Kelas::class, 'kelas_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'nama_petugas' => $this->nama_petugas
        ];
    }
}