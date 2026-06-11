<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    // Karena tabel ini hanya berisi satu baris konfigurasi, kita matikan fitur timestamps (created_at) jika tidak pakai
    // Tetapi di database kita pakai updated_at, jadi kita biarkan true atau atur kastem
    public $timestamps = false;

    protected $fillable = [
        'current_version', // Contoh isi: '1.0.5'
        'download_url',    // Contoh isi: 'https://domain-anda.com/download/app-v1.0.5.apk'
        'is_force_update', // Isi: 1 (Wajib update) atau 0 (Opsional)
    ];

    protected $casts = [
        'is_force_update' => 'boolean',
        'updated_at'      => 'datetime',
    ];
}