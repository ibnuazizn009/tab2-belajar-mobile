<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenjangSekolah extends Model
{
    use HasFactory;

    protected $table = 'jenjang_sekolah';

    public $timestamps = false;

    protected $fillable = [
        'nama_jenjang',
    ];

    /**
     * Relasi ke daftar sekolah yang menggunakan jenjang ini (misal: SD, SMP, SMA)
     */
    public function sekolahs(): HasMany
    {
        return $this->hasMany(Sekolah::class, 'jenjang_id', 'id');
    }
}