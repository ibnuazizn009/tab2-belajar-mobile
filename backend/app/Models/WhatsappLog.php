<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $table = 'whatsapp_logs';

    // Kita hanya butuh mencatat kapan pesan dibuat (created_at), tidak butuh updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'no_tujuan',     // Nomor WA orang tua (contoh: '08123456789')
        'pesan',         // Isi teks berita acara tabungan
        'status',        // pending, sent, failed
        'error_message', // Jika gagal, simpan alasan error dari API gateway di sini
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}