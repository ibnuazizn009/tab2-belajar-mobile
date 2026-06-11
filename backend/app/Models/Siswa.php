<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'sekolah_id',   
        'kelas_id',
        'nis',
        'nama_siswa',    
        'no_wa_orang_tua', 
        'saldo',
        'is_active', 
    ];

    /**
     * Relasi ke tabel sekolah
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'id');
    }

    /**
     * Relasi ke tabel kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id');
    }

    /**
     * Relasi ke riwayat transaksi siswa
     */
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'siswa_id', 'id'); 
    }
}