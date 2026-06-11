<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'sekolah_id',
        'nama_kelas',
        'tingkat',
        'guru_id',
    ];

    /**
     * Relasi ke tabel sekolah
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'id');
    }

    /**
     * Relasi ke wali kelas (guru)
     */
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(LoginUser::class, 'guru_id', 'id');
    }

    /**
     * Relasi ke daftar siswa di dalam kelas ini
     */
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id', 'id');
    }
}