<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/dashboard-admin', function () {
    return view('dashboard-admin');
})->name('dashboard-admin');

Route::get('/akun-guru', function () { return view('akun-guru'); });
Route::get('/data-guru', function () { return view('data-guru'); });

Route::get('/kelas', function () { return view('kelas'); });
Route::get('/transaksi', function () { return view('transaksi'); });
Route::get('/pengaturan', function () { return view('pengaturan'); });

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', [SuperAdminController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [SuperAdminController::class, 'registerSekolahBaru'])->name('register.proses');

Route::get('/test-no-auth', function () {
    return 'Halaman ini berhasil diakses tanpa redirect';
});
