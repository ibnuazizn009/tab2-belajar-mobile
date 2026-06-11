<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sekolah;
use App\Models\LoginUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function registerSekolahBaru(Request $request)
    {
        $request->validate([
            'npsn'          => 'required|string|size:8|unique:sekolah,npsn', // Wajib 8 digit & unik
            'nama_sekolah'  => 'required|string|max:255',
            'jenjang_id'    => 'required|integer',
            'status'        => 'required|in:NEGERI,SWASTA',
            'alamat'        => 'nullable|string',
            'kota_id'       => 'required|integer',
            
            'nama_lengkap'  => 'required|string|max:255',
            'no_whatsapp'   => 'required|string|max:20',
            'username'      => 'required|string|min:4|unique:login_user,username',
            'password'      => 'required|string|min:6',
        ]);

        DB::beginTransaction();

        try {
            // Kita berikan bonus free trial 7 hari secara otomatis saat pertama daftar
            $sekolah = Sekolah::create([
                'npsn'               => $request->npsn,
                'nama_sekolah'       => strtoupper($request->nama_sekolah),
                'jenjang_id'         => $request->jenjang_id,
                'status'             => $request->status,
                'alamat'             => $request->alamat,
                'kota_id'            => $request->kota_id,
                'is_premium'         => 1, 
                'premium_expires_at' => Carbon::now()->addDays(7), 
            ]);

            $admin = LoginUser::create([
                'sekolah_id'   => $sekolah->id,
                'username'     => strtolower($request->username),
                'password'     => Hash::make($request->password),
                'nama_petugas' => $request->nama_lengkap,
                'no_whatsapp'  => $request->no_whatsapp,
                'role'         => 'admin_sekolah', // 👈 TINGKATAN TERTINGGI DI SEKOLAH TERSEBUT
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Sekolah dan Akun Admin berhasil didaftarkan! Silakan login ke Dashboard Web Anda.',
                'data'    => [
                    'nama_sekolah' => $sekolah->nama_sekolah,
                    'username_admin' => $admin->username,
                    'berlaku_sampai' => $sekolah->premium_expires_at->format('Y-m-d H:i:s')
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mendaftarkan sekolah, terjadi kesalahan sistem.'
            ], 500);
        }
    }
}