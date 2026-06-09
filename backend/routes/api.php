<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Siswa\SiswaController;
use App\Http\Controllers\Siswa\TransaksiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$prefix = 'services/tab2one';

// Public routes
Route::post("$prefix/auth/login", [AuthController::class, 'login']);
Route::post("$prefix/auth/register", [AuthController::class, 'register']);

Route::get("$prefix/master/kelas", [MasterController::class, 'getAllKelas']);
Route::get("$prefix/master/kota", [MasterController::class, 'getAllKota']);
Route::get("$prefix/master/sekolah-by-kota", [MasterController::class, 'getSekolahByKota']);

Route::middleware('auth.jwt')->group(function () use ($prefix) {
    Route::post("$prefix/auth/logout",  [AuthController::class, 'logout']);
    Route::post("$prefix/auth/refresh", [AuthController::class, 'refresh']);
    Route::get("$prefix/auth/me",       [AuthController::class, 'me']);

    Route::get("$prefix/siswa/kelas", [SiswaController::class, 'getDataKelas']);
    Route::get("$prefix/siswa/siswa-per-kelas", [SiswaController::class, 'getDataSiswaPerKelas']);
    Route::get("$prefix/siswa/transaksi-per-kelas", [SiswaController::class, 'getDataTransaksiSiswaPerKelas']);
    Route::get("$prefix/siswa/transaksi-tanggal", [SiswaController::class, 'getDataTransaksiTanggal']);
    Route::post("$prefix/siswa/tambah-siswa",  [SiswaController::class, 'postTambahSiswa']);
    Route::get("$prefix/siswa/laporan-keuangan-siswa",  [SiswaController::class, 'getLaporanKeuanganSiswa']);
    Route::get("$prefix/siswa/laporan-transaksi-siswa",  [SiswaController::class, 'getLaporanTransaksiSiswa']);

    Route::post("$prefix/transaksi/transaksi",  [TransaksiController::class, 'postTransaksiSiswa']);
    Route::get("$prefix/transaksi/riwayat-transaksi",  [TransaksiController::class, 'getRiwayatTransaksiSiswa']);

    // Route::get("$prefix/siswa",                   [SiswaController::class, 'getDataSiswa']);
    // Route::get("$prefix/siswa-bykelas",            [SiswaController::class, 'getDataSiswaByKelas']);
    // Route::get("$prefix/siswa/{nis}",              [SiswaController::class, 'getDataSiswaByNis']);
    // Route::post("$prefix/registrasi/siswa/add",   [SiswaController::class, 'postDataSiswa']);
    // Route::post("$prefix/registrasi/siswa/{nis}", [SiswaController::class, 'editDataSiswa']);
    // Route::post("$prefix/registrasi/siswa",       [SiswaController::class, 'deleteDataSiswa']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
