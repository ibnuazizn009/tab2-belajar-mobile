<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    const UPDATED_AT = null;

    protected $fillable = [
        'siswa_id',
        'user_id',
        'tipe',
        'nominal',
        'saldo_awal',
        'saldo_akhir',
        'keterangan',
    ];

    protected $casts = [
        'tipe'        => 'string', // enum('setor','tarik')
        'nominal'     => 'integer',
        'saldo_awal'  => 'integer',
        'saldo_akhir' => 'integer',
        'created_at'  => 'datetime',
    ];

    /**
     * Relasi ke data siswa yang melakukan transaksi
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }

    /**
     * Relasi ke data guru (petugas) yang menginput transaksi ini
     * Sekarang merujuk ke data_gurus, bukan login_users
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(DataGuru::class, 'user_id', 'id');
    }
}