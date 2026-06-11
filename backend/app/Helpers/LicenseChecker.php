<?php

namespace App\Helpers;

use App\Models\Siswa;
use App\Models\LoginUser;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LicenseChecker
{
    /**
     * Mengecek apakah sekolah masih aktif atau sudah kedaluwarsa
     */
    public static function checkStatus($sekolah)
    {
        if (!$sekolah) return 'frozen';

        // Jika paket FREE dan sudah lewat 7 hari sejak dibuat
        if ($sekolah->paket_layanan === 'free') {
            $createdAt = Carbon::parse($sekolah->created_at);
            if (Carbon::now('Asia/Jakarta')->diffInDays($createdAt) > 7) {
                return 'frozen';
            }
        }

        // Jika paket MIDDLE/GOLDEN dan melewati tgl kedaluwarsa
        if (in_array($sekolah->paket_layanan, ['middle', 'golden'])) {
            if ($sekolah->premium_expires_at && Carbon::now('Asia/Jakarta')->greaterThan($sekolah->premium_expires_at)) {
                return 'frozen';
            }
        }

        return 'active';
    }

    /**
     * Validasi batasan saat tambah murid baru
     */
    public static function canAddSiswa($sekolahId)
    {
        $sekolah = DB::table('sekolah')->where('id', $sekolahId)->first();
        if (self::checkStatus($sekolah) === 'frozen') return false;

        if ($sekolah->paket_layanan === 'free') {
            $currentSiswa = Siswa::where('sekolah_id', $sekolahId)->count();
            if ($currentSiswa >= 10) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validasi batasan saat tambah petugas/guru baru
     */
    public static function canAddPetugas($sekolahId)
    {
        $sekolah = DB::table('sekolah')->where('id', $sekolahId)->first();
        if (self::checkStatus($sekolah) === 'frozen') return false;

        if ($sekolah->paket_layanan === 'free') {
            $currentPetugas = LoginUser::where('sekolah_id', $sekolahId)
                ->whereIn('role', ['admin_sekolah', 'guru'])
                ->count();
            if ($currentPetugas >= 2) {
                return false;
            }
        }
        return true;
    }

    /**
     * Cek kuota WhatsApp hari ini
     */
    public static function canSendWhatsApp($sekolahId)
    {
        $sekolah = DB::table('sekolah')->where('id', $sekolahId)->first();
        if (self::checkStatus($sekolah) === 'frozen') return false;

        if ($sekolah->paket_layanan === 'free') {
            return false; 
        }

        if ($sekolah->paket_layanan === 'middle') {
            $todayWaCount = Transaksi::whereHas('siswa', function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId);
                })
                ->whereDate('created_at', Carbon::now('Asia/Jakarta')->startOfDay())
                ->where('wa_sent_status', 1) 
                ->count();

            if ($todayWaCount >= 10) {
                return false;
            }
        }

        return true; 
    }
}