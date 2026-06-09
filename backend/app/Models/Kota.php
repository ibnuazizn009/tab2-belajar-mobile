<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kota extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'kota';

    // Kolom yang diizinkan untuk pengisian massal (Mass Assignment)
    protected $fillable = [
        'nama_kota',
        'provinsi',
    ];

    /**
     * Relasi One-to-Many: Satu Kota memiliki banyak Sekolah.
     */
    public function sekolah(): HasMany
    {
        return $this->hasMany(Sekolah::class, 'kota_id', 'id');
    }
}