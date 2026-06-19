<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\LoginUser;
use App\Models\Kelas; // 👈 1. Import model Kelas di bagian atas
use Carbon\Carbon;
use App\Helpers\LicenseChecker;
use Illuminate\Support\Facades\Cookie;

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
            
                // Cek apakah masa premium sudah kedaluwarsa
                $isExpired = $sekolah->premium_expires_at && Carbon::now('Asia/Jakarta')->greaterThan($sekolah->premium_expires_at);
                $notActive = !$sekolah->premium_expires_at && !$sekolah->is_premium;
            
                if ($isExpired || $notActive) {
                    if ($user->role !== 'admin_sekolah') {
                        return response()->json([
                            'status'  => 'frozen',
                            'message' => 'Masa aktif layanan sekolah Anda telah berakhir. Silakan hubungi Admin Sekolah untuk perpanjangan.'
                        ], 403);
                    }
                    
                    // 💡 JIKA DIA ADALAH ADMIN SEKOLAH, JANGAN DI-BLOKIR (Biarkan lolos ke dashboard)
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

            // Hitung sisa hari paket secara dinamis
            $sisaHariPaket = null;
            if ($user->sekolah) {
                if ($user->sekolah->paket_layanan === 'free') {
                    $sisaHariPaket = max(0, 7 - \Carbon\Carbon::parse($user->sekolah->created_at)->diffInDays(\Carbon\Carbon::now('Asia/Jakarta')));
                } else {
                    // Jika paket berbayar/premium, hitung selisih dari tanggal premium_expires_at ke hari ini
                    if ($user->sekolah->premium_expires_at) {
                        $expiredDate = \Carbon\Carbon::parse($user->sekolah->premium_expires_at);
                        $now = \Carbon\Carbon::now('Asia/Jakarta');
                        
                        $sisaHariPaket = $now->greaterThan($expiredDate) ? 0 : max(0, $now->diffInDays($expiredDate, false));
                    } else {
                        $sisaHariPaket = 0;
                    }
                }
            }

            $responseData = [
                'success'      => true,
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
                    'paket_layanan'   => $user->sekolah ? $user->sekolah->paket_layanan : 'free',
                    'status_akun'     => $statusAkun,
                    'sisa_hari_paket' => $sisaHariPaket,
                    'expired_at'      => $user->sekolah ? $user->sekolah->premium_expires_at : null,
                ],
            ];

            // Mobile (Expo) WAJIB kirim header ini, default-nya dianggap web/Blade
            $isMobileClient = $request->header('X-Client-Platform') === 'mobile';

            if (!$isMobileClient) {
                
                $cookieMinutes = config('jwt.ttl');
                $isProduction  = config('app.env') === 'production';

                return response()->json($responseData)
                    ->cookie(
                        'token_jwt',
                        $token,
                        $cookieMinutes,
                        '/',
                        null,                 // domain: auto-detect, jangan hardcode IP/domain
                        $isProduction,        // secure: true di production (HTTPS wajib)
                        true,                 // httpOnly: tidak bisa diakses JS / localStorage
                        false,                // raw
                        'Lax'                 // sameSite cukup untuk form same-origin
                    );
            }

            $responseData['access_token'] = $token;

            return response()->json($responseData);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error AuthController: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            auth('api')->logout();
        } catch (\Exception $e) {
            // Tetap lanjut jika token ternyata sudah kadaluwarsa duluan
        }

        // 2. Cek apakah request datang dari Web Browser (AJAX/Fetch)
        if ($request->wantsJson() || $request->ajax()) {
            // JALUR WEB: Hapus Cookie 'token_jwt' di browser
            $cookie = Cookie::forget('token_jwt');

            return response()->json([
                'success' => true,
                'message' => 'Berhasil keluar dari sistem Web.'
            ])->withCookie($cookie);
        }

        // 3. JALUR MOBILE: Kembalikan JSON murni saja (tanpa cookie)
        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar dari sistem Mobile.'
        ]);
    }
}