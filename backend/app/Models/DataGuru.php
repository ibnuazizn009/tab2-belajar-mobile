<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataGuru extends Model
{
    use HasFactory;

    protected $table = 'data_gurus';

    protected $fillable = [
        'sekolah_id',
        'login_user_id',
        'nip',
        'nama_guru',
        'jenis_kelamin',
        'no_hp',
        'email',
    ];

    /**
     * Relasi ke tabel Sekolah (Many to One)
     * Hubungan ke pemilik sekolah asal guru tersebut
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'id');
    }

    public function loginUser()
    {
        return $this->belongsTo(LoginUser::class, 'login_user_id');
    }
}