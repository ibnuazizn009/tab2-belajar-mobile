<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class LoginUser extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'login_users';

    protected $fillable = [
        'sekolah_id', 
        'username',
        'password',
        'nama_lengkap',
        'no_whatsapp', 
        'role',       
    ];

    /**
     * Relasi ke tabel sekolah
     * CONSTRAINT "login_users_ibfk_1" FOREIGN KEY ("sekolah_id") REFERENCES "sekolah" ("id")
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'id');
    }

    /**
     * Relasi ke tabel kelas yang diampu oleh guru ini
     * CONSTRAINT "kelas_ibfk_2" FOREIGN KEY ("guru_id") REFERENCES "users" ("id")
     */
    public function kelasYangDipegang(): HasMany
    {
        return $this->hasMany(Kelas::class, 'guru_id', 'id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'id'           => $this->id,
            'username'     => $this->username,
            'nama_lengkap' => $this->nama_lengkap,
            'sekolah_id'   => $this->sekolah_id,
            'role'         => $this->role
        ];
    }
}