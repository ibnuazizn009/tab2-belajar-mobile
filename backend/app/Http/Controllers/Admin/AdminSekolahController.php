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
use App\Models\DataGuru;

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
    public function tambahDataGuru(Request $request)
    {
        /** @var \App\Models\LoginUser $admin */
        $admin = auth('api')->user();

        // 1. VALIDASI INPUT SESUAI STRUKTUR DATA GURU (PROFIL)
        $validator = Validator::make($request->all(), [
            'nama_guru'     => 'required|string|max:255',
            'nip'           => 'nullable|string|max:50|unique:data_gurus,nip', // Unik di tabel data_gurus
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp'         => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $sekolah = $admin->sekolah;
        
        $jumlahGuruSekarang = DataGuru::where('sekolah_id', $admin->sekolah_id)->count();

        if ($sekolah) {
            // Pengecekan limitasi paket layanan sekolah
            if ($sekolah->paket_layanan === 'free' && $jumlahGuruSekarang >= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas limit data Guru untuk Paket Free telah penuh (Maksimal 1 Guru). Silakan upgrade paket layanan sekolah Anda.'
                ], 403);
            } 
            
            if ($sekolah->paket_layanan === 'middle' && $jumlahGuruSekarang >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas limit data Guru untuk Paket Middle telah penuh (Maksimal 5 Guru).'
                ], 403);
            }
        }

        $guru = DataGuru::create([
            'sekolah_id'    => $admin->sekolah_id, // Otomatis mengikat ke ID sekolah milik admin
            'nip'           => $request->nip,
            'nama_guru'     => $request->nama_guru,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp'         => $request->no_hp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Guru atas nama ' . $guru->nama_guru . ' berhasil disimpan.'
        ], 201);
    }

    public function getDataGuru()
    {
        /** @var \App\Models\LoginUser $admin */
        $admin = auth('api')->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'
            ], 401);
        }

        $dataGuru = DataGuru::where('sekolah_id', $admin->sekolah_id)
                            ->latest()
                            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat daftar data guru.',
            'data'    => $dataGuru
        ], 200);
    }

    /**
     * 3. Reset Password Akun Guru oleh Admin Sekolah
     */
    public function resetPasswordGuru(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:login_users,username',
            'password' => 'required|string|min:4',
        ], [
            'username.exists' => 'Username tidak terdaftar di sistem.',
            'password.min'    => 'Password minimal harus 4 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Ambil sekolah_id admin yang login untuk validasi keamanan tambahan
            $sekolahId = auth()->user()->sekolah_id ?? null;

            // Cari guru spesifik berdasarkan username & sekolah_id milik admin ini saja
            $guru = LoginUser::where('username', $request->username)
                ->where('sekolah_id', $sekolahId)
                ->where('role', 'guru')
                ->first();

            if (!$guru) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun guru tidak ditemukan atau Anda tidak memiliki hak akses.'
                ], 404);
            }

            // Update password (disimpan plain text sesuai kebutuhan struktur tabel Anda)
            $guru->password = $request->password;
            $guru->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset password: ' . $e->getMessage()
            ], 500);
        }
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

    public function getDataAkunGuru(Request $request)
    {
        try {
            $sekolahId = auth()->user()->sekolah_id ?? null;

            if (!$sekolahId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi tidak valid atau sekolah tidak ditemukan.'
                ], 401);
            }

            $guru = LoginUser::where('sekolah_id', $sekolahId)
                ->where('role', 'guru')
                ->select('nama_lengkap', 'username', 'password') // Mengambil password untuk fitur lihat password di tabel
                ->get();

            return response()->json([
                'success' => true,
                'guru'    => $guru
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tambahDataAkunGuru(Request $request)
    {
        // Validasi Inputan Form
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:login_users,username',
            'password'     => 'required|string|min:4',
        ], [
            'username.unique' => 'Username ini sudah terdaftar di sistem.',
            'password.min'    => 'Password minimal harus 4 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Ambil sekolah_id dari admin yang sedang login
            $sekolahId = auth()->user()->sekolah_id ?? null;

            if (!$sekolahId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi admin telah berakhir, silakan login kembali.'
                ], 401);
            }
            $paketLayanan = auth()->user()->paket_layanan ?? 'free'; 

            if (strtolower($paketLayanan) === 'free') {
                // Hitung total akun dengan role 'guru' yang sudah ada di sekolah ini
                $jumlahGuruSoreIni = LoginUser::where('sekolah_id', $sekolahId)
                    ->where('role', 'guru')
                    ->count();

                if ($jumlahGuruSoreIni >= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Batas Paket Free Tercapai! Paket gratis hanya diperbolehkan memiliki maksimal 1 akun guru. Silakan upgrade paket untuk menambahkan akun tanpa batas.'
                    ], 422); // Status 422 untuk Unprocessable Entity / Isian Ditolak
                }
            }

            LoginUser::create([
                'sekolah_id'   => $sekolahId,
                'username'     => $request->username,
                'password'     => $request->password, 
                'nama_lengkap' => $request->nama_lengkap,
                'no_whatsapp'  => null,
                'role'         => 'guru', 
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Akun guru berhasil didaftarkan.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}