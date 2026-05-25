<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabelTransaksi extends Model
{
    protected $table = 'tabel_transaksi';

    protected $fillable = [
        'nis',
        'tipe',
        'nominal',
    ];

    protected $casts = [
        'tipe'    => 'string',
        'nominal' => 'integer',
    ];

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'nis', 'nis');
    }
}