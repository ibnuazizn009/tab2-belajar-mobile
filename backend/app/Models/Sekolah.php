<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolah';

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'email_sekolah',
        'jenjang_id',
        'status',    
        'alamat',
        'kota_id',
        'is_premium',
        'paket_layanan',         
        'premium_expires_at',
        'status_pembayaran',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'premium_expires_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel kota
     */
    public function kota(): BelongsTo
    {
        return $this->belongsTo(Kota::class, 'kota_id', 'id');
    }

    /**
     * Relasi ke tabel jenjang_sekolah
     */
    public function jenjang(): BelongsTo
    {
        return $this->belongsTo(JenjangSekolah::class, 'jenjang_id', 'id');
    }

    /**
     * Relasi ke daftar kelas di sekolah ini
     */
    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'sekolah_id', 'id');
    }

    /**
     * Relasi ke daftar semua siswa di sekolah ini
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'sekolah_id', 'id');
    }
}