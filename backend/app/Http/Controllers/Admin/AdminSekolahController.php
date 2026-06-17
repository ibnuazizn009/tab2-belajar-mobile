<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\LoginUser;
use App\Models\Kelas;

class AdminSekolahController extends Controller
{
    /**
     * 1. Ambil Data Ringkasan Dashboard Web Admin
     */
    public function getDashboardData()
    {
        /** @var \App\Models\LoginUser $admin */
        $admin = auth('api')->user(); // Menggunakan guard api sesuai fungsi login Anda

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil dimuat.',
            'data'    => [
                'admin_info' => [
                    'id'           => $admin->id,
                    'nama_lengkap' => $admin->nama_lengkap,
                    'username'     => $admin->username,
                    'foto_profil'  => $admin->foto_profil ? asset('storage/' . $admin->foto_profil) : null
                ],
                'ringkasan' => [
                    'total_siswa'        => 248, // Nanti di-query dari tabel siswa berdasarkan sekolah_id
                    'total_guru'         => LoginUser::where('sekolah_id', $admin->sekolah_id)->where('role', 'guru')->count(),
                    'total_tabungan'     => 14250000,
                    'transaksi_hari_ini' => 32
                ],
                'grafik' => [
                    'labels'    => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                    'setoran'   => [450000, 600000, 350000, 780000, 910000, 200000],
                    'penarikan' => [100000, 150000, 50000, 300000, 120000, 90000]
                ]
            ]
        ], 200);
    }

    /**
     * 2. Tambah Akun Guru Berdasarkan Tier Paket Layanan
     */
    public function tambahGuru(Request $request)
    {
        /** @var \App\Models\LoginUser $admin */
        $admin = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|min:4|unique:login_users,username', // Sesuaikan nama tabel asli login_user Anda
            'password'     => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // --- VALIDASI PEMBATASAN KUOTA BERDASARKAN PAKET LAYANAN ---
        // Anda bisa memuat relasi sekolah si admin untuk dicek paketnya
        $sekolah = $admin->sekolah;
        $jumlahGuruSekarang = LoginUser::where('sekolah_id', $admin->sekolah_id)->where('role', 'guru')->count();

        if ($sekolah) {
            // Contoh limitasi: paket free maks 1 guru, middle maks 5 guru, golden/premium unlimited
            if ($sekolah->paket_layanan === 'free' && $jumlahGuruSekarang >= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas limit akun Guru untuk Paket Free telah penuh (Maksimal 1 Guru). Silakan upgrade paket layanan sekolah Anda.'
                ], 403);
            } 
            
            if ($sekolah->paket_layanan === 'middle' && $jumlahGuruSekarang >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas limit akun Guru untuk Paket Middle telah penuh (Maksimal 5 Guru).'
                ], 403);
            }
        }

        $guru = LoginUser::create([
            'sekolah_id'   => $admin->sekolah_id,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => strtolower(trim($request->username)),
            'password'     => Hash::make($request->password),
            'role'         => 'guru',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun Guru atas nama ' . $guru->nama_lengkap . ' berhasil didaftarkan.'
        ], 201);
    }

    /**
     * 3. Reset Password Akun Guru oleh Admin Sekolah
     */
    public function resetPasswordGuru(Request $request)
    {
        /** @var \App\Models\LoginUser $admin */
        $admin = auth('api')->user();

        $request->validate([
            'username_guru' => 'required|string',
        ]);

        $guru = LoginUser::where('username', $request->username_guru)
                         ->where('sekolah_id', $admin->sekolah_id)
                         ->where('role', 'guru')
                         ->first();

        if (!$guru) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Guru tidak ditemukan di bawah naungan sekolah Anda.'
            ], 404);
        }

        $guru->password = Hash::make('123456');
        $guru->save();

        return response()->json([
            'success' => true,
            'message' => 'Password guru ' . $guru->nama_lengkap . ' berhasil di-reset menjadi standar: 123456'
        ], 200);
    }

    /**
     * 4. Ubah Password Mandiri (Admin Sendiri / Guru Sendiri)
     */
    public function updatePasswordSelf(Request $request)
    {
        /** @var \App\Models\LoginUser $user */
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini yang Anda masukkan salah.'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password akun Anda berhasil diubah.'
        ], 200);
    }

    /**
     * 5. Ubah Foto Profil Mandiri
     */
    public function updateFotoProfil(Request $request)
    {
        /** @var \App\Models\LoginUser $user */
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'File harus berupa gambar (JPG/PNG) dengan ukuran maksimal 2MB.'
            ], 422);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $pathFile = $request->file('foto')->store('profiles', 'public');

            $user->foto_profil = $pathFile;
            $user->save();

            return response()->json([
                'success'  => true,
                'message'  => 'Foto profil berhasil diperbarui.',
                'foto_url' => asset('storage/' . $pathFile)
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengunggah gambar.'
        ], 400);
    }
}