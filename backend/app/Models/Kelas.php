<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
    ];

    // Relasi ke siswa
    public function siswas()
    {
        return $this->hasMany(MasterSiswa::class, 'kelas_id');
    }

    // Relasi ke petugas (wali kelas)
    public function petugas()
    {
        return $this->hasMany(LoginUser::class, 'kelas_id');
    }
}