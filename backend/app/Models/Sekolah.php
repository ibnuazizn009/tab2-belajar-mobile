<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sekolah extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'sekolah';

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'status',
        'alamat',
        'kota_id',
    ];

    /**
     * Relasi Many-to-One: Sekolah ini dimiliki oleh/berada di sebuah Kota.
     */
    public function kota(): BelongsTo
    {
        return $this->belongsTo(Kota::class, 'kota_id', 'id');
    }
}