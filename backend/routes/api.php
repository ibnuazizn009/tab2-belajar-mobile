<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Siswa\SiswaController;
use App\Http\Controllers\Siswa\TransaksiController;
use App\Http\Controllers\Api\AdminSekolahController;
use App\Http\Controllers\SuperAdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

$prefix = 'services/tab2one';

// Pintu masuk login aplikasi HP Guru & Web Admin
Route::post("$prefix/auth/login", [AuthController::class, 'login']);

Route::post("$prefix/auth/register-sekolah", [SuperAdminController::class, 'registerSekolahBaru']);

// Data Master Umum (Digunakan saat pendaftaran sekolah baru di website)
Route::get("$prefix/master/kota", [MasterController::class, 'getAllKota']);
Route::get("$prefix/master/sekolah-by-kota", [MasterController::class, 'getSekolahByKota']);

// Menggunakan standar middleware Tymon/JWTAuth yaitu 'auth:api'
Route::middleware('auth:api')->group(function () use ($prefix) {
    
    // Auth Kelola Session
    Route::post("$prefix/auth/logout",  [AuthController::class, 'logout']);
    Route::post("$prefix/auth/refresh", [AuthController::class, 'refresh']);
    Route::get("$prefix/auth/me",       [AuthController::class, 'me']);

    Route::get("$prefix/master/kelas", [MasterController::class, 'getAllKelas']);

    // Manajemen Siswa (Aplikasi HP Guru & Web Admin)
    Route::get("$prefix/siswa/kelas", [SiswaController::class, 'getDataKelas']);
    Route::get("$prefix/siswa/siswa-per-kelas", [SiswaController::class, 'getDataSiswaPerKelas']);
    Route::get("$prefix/siswa/transaksi-per-kelas", [SiswaController::class, 'getDataTransaksiSiswaPerKelas']);
    Route::get("$prefix/siswa/transaksi-tanggal", [SiswaController::class, 'getDataTransaksiTanggal']);
    Route::post("$prefix/siswa/tambah-siswa",  [SiswaController::class, 'postTambahSiswa']);
    Route::get("$prefix/siswa/laporan-transaksi-siswa",  [SiswaController::class, 'getLaporanTransaksiSiswa']);
    Route::get("$prefix/siswa/laporan-keuangan-siswa", [SiswaController::class, 'getLaporanKeuanganSiswa']);
    Route::get("$prefix/siswa/template", [SiswaController::class, 'downloadTemplate']);
    Route::post("$prefix/siswa/import-excel", [SiswaController::class, 'importExcel']);

    Route::post("$prefix/transaksi/transaksi",  [TransaksiController::class, 'postTransaksiSiswa']);
    Route::get("$prefix/transaksi/riwayat-transaksi",  [TransaksiController::class, 'getRiwayatTransaksiSiswa']);

    Route::middleware('role:ADMIN_SEKOLAH')->group(function () use ($prefix) {
        
        Route::get("$prefix/admin/dashboard", [AdminSekolahController::class, 'getDashboardData']);
        
        Route::post("$prefix/admin/guru", [AdminSekolahController::class, 'tambahGuru']);
        Route::post("$prefix/admin/guru/reset-password", [AdminSekolahController::class, 'resetPasswordGuru']);
        
        Route::post("$prefix/admin/update-foto", [AdminSekolahController::class, 'updateFotoProfil']);
        Route::post("$prefix/admin/update-password", [AdminSekolahController::class, 'updatePasswordSelf']);
        
        Route::post("$prefix/siswa/import-excel", [SiswaController::class, 'importExcel']);
    });

});