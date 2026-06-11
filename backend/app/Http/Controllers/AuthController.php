<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\LoginUser;
use App\Models\Kelas; // 👈 1. Import model Kelas di bagian atas
use Carbon\Carbon;
use App\Helpers\LicenseChecker;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = LoginUser::with('sekolah')
                ->where('username', $request->username)
                ->orWhere('username', strtolower($request->username))
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Username atau password salah.'], 401);
            }

            if ($user->role !== 'super_admin' && $user->sekolah) {
                $sekolah = $user->sekolah;
                
                if ($sekolah->premium_expires_at) {
                    if (Carbon::now('Asia/Jakarta')->greaterThan($sekolah->premium_expires_at)) {
                        return response()->json([
                            'status'  => 'frozen',
                            'message' => 'Masa aktif uji coba (trial) atau layanan premium sekolah Anda telah berakhir. Silakan hubungi Admin Utama untuk perpanjangan.'
                        ], 403);
                    }
                } else {
                    if (!$sekolah->is_premium) {
                        return response()->json([
                            'status'  => 'frozen',
                            'message' => 'Sekolah Anda belum mengaktifkan masa uji coba. Silakan hubungi Admin Utama.'
                        ], 403);
                    }
                }
            }

            try {
                if (!$token = auth('api')->login($user)) {
                    return response()->json(['success' => false, 'message' => 'Username atau password salah.'], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['success' => false, 'message' => 'Gagal membuat token, coba lagi.'], 500);
            }

            $kelas = Kelas::where('guru_id', $user->id)
                          ->where('sekolah_id', $user->sekolah_id)
                          ->first();

            $statusAkun = LicenseChecker::checkStatus($user->sekolah);
            
            return response()->json([
                'success'      => true,
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => config('jwt.ttl') * 60,
                'user'         => [
                    'id'           => $user->id,
                    'username'     => $user->username,
                    'nama_lengkap' => $user->nama_lengkap,
                    'sekolah_id'   => (int) $user->sekolah_id,
                    'nama_sekolah' => $user->sekolah ? $user->sekolah->nama_sekolah : 'Super Admin Panel',
                    'role'         => $user->role, 
                    'kelas_id'     => $kelas ? $kelas->id : null, 
                    'sekolah'      => $user->sekolah,
                    'paket_layanan'   => $user->sekolah ? $user->sekolah->paket_layanan : 'free', // 'free', 'middle', 'golden'
                    'status_akun'     => $statusAkun, // 'active' atau 'frozen'
                    'sisa_hari_free'  => $user->sekolah && $user->sekolah->paket_layanan === 'free' 
                                            ? max(0, 7 - \Carbon\Carbon::parse($user->sekolah->created_at)->diffInDays(\Carbon\Carbon::now('Asia/Jakarta')))
                                            : null,
                    'expired_at'      => $user->sekolah ? $user->sekolah->premium_expires_at : null, 
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error AuthController: ' . $e->getMessage()], 500);
        }
    }
}