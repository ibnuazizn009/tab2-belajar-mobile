<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSiswa extends Model
{
    protected $table = 'master_siswa';

    // Primary key bukan 'id' untuk relasi transaksi, tapi tetap pakai id
    protected $fillable = [
        'nis',
        'nama',
        'kelas_id',
        'saldo',
    ];

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke Transaksi
    public function transaksis()
    {
        return $this->hasMany(TabelTransaksi::class, 'nis', 'nis');
    }
}