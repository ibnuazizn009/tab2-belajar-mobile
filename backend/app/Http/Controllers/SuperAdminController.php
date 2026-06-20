<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sekolah;
use App\Models\LoginUser;
use App\Models\JenjangSekolah;
use App\Models\Kota;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class SuperAdminController extends Controller
{
    public function showRegisterForm()
    {
        $jenjangs = JenjangSekolah::orderBy('id', 'asc')->get();

        $kotas = Kota::orderBy('nama_kota', 'asc')->get();

        $paket_terpilih = request()->query('paket', 'BRONZE');

        return view('auth.register', compact('jenjangs', 'kotas', 'paket_terpilih'));
    }

    public function registerSekolahBaru(Request $request)
    {
        try {
            $request->validate([
                'npsn'          => 'required|string|size:8|unique:sekolah,npsn',
                'nama_sekolah'  => 'required|string|max:255',
                'jenjang_id'    => 'required|numeric', 
                'status'        => 'required|in:NEGERI,SWASTA',
                'alamat'        => 'nullable|string',
                'kota_id'       => 'required|numeric', 
                'paket_layanan' => 'required|in:BRONZE,SILVER,GOLDEN', 
                
                'nama_lengkap'  => 'required|string|max:255',
                'no_whatsapp'   => 'required|string|max:20',
                'username'      => 'required|string|min:4|unique:login_users,username',
                'password'      => 'required|string|min:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal di server.',
                'errors'  => $e->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            if ($request->paket_layanan === 'BRONZE') {
                $isPremium = 0; 
                $expiresAt = Carbon::now()->addDays(7); // Masa trial BRONZE 7 hari
            } elseif ($request->paket_layanan === 'SILVER') {
                $isPremium = 1; 
                $expiresAt = Carbon::now()->addDays(30); // Masa aktif SILVER 30 hari
            } else { // GOLDEN
                $isPremium = 1; 
                $expiresAt = Carbon::now()->addDays(30); // Masa aktif GOLDEN 30 hari
            }

            // Simpan data Sekolah
            $sekolah = Sekolah::create([
                'npsn'               => $request->npsn,
                'nama_sekolah'       => strtoupper($request->nama_sekolah),
                'jenjang_id'         => $request->jenjang_id,
                'status'             => $request->status,
                'alamat'             => $request->alamat,
                'kota_id'            => $request->kota_id,
                'is_premium'         => $isPremium,
                'paket_layanan'      => $request->paket_layanan,
                'premium_expires_at' => $expiresAt, 
            ]);

            // Simpan data Akun Admin Sekolah
            $admin = LoginUser::create([
                'sekolah_id'   => $sekolah->id,
                'username'     => strtolower($request->username),
                'password'     => Hash::make($request->password),
                'nama_lengkap' => $request->nama_lengkap,
                'no_whatsapp'  => $request->no_whatsapp,
                'role'         => 'admin_sekolah',
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Sekolah dan Akun Admin berhasil didaftarkan!',
                'data'    => [
                    'nama_sekolah'   => $sekolah->nama_sekolah,
                    'is_premium'     => $sekolah->is_premium,
                    'username'       => $admin->username,
                    'berlaku_sampai' => $sekolah->premium_expires_at->format('Y-m-d H:i:s')
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mendaftarkan sekolah, terjadi kesalahan sistem.',
                'error'   => $e->getMessage() // Opsional: Hapus baris ini saat aplikasi sudah live/production
            ], 500);
        }
    }
}