<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\LoginUser;
use App\Models\Kelas;
use App\Models\DataGuru;
use App\Models\Transaksi;
use App\Models\Sekolah;
use Illuminate\Validation\Rules\Password;

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
            $guru->password = Hash::make($request->password);
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

    public function updatePasswordSelf(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);
    
        $authUser = auth('api')->user();
        if (!$authUser) {
            return response()->json(['success' => false, 'message' => 'Tidak terautentikasi.'], 401);
        }
    
        $loginUser = LoginUser::find($authUser->id);
        if (!$loginUser) {
            return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan.'], 404);
        }
    
        if (!Hash::check($request->current_password, $loginUser->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama yang Anda masukkan salah.'
            ], 422);
        }
    
        $loginUser->password = Hash::make($request->new_password);
        $loginUser->is_use = false; // Force logout — wajib login ulang dengan password baru
        $loginUser->save();
    
        try {
            auth('api')->logout();
        } catch (\Exception $e) {
            // Token mungkin sudah tidak valid, abaikan — is_use sudah ke-update
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui. Silakan login kembali dengan password baru Anda.',
            'force_logout' => true,
        ]);
    }

    public function updateFotoProfil(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // max 2MB
        ]);

        $authUser = auth('api')->user();
        if (!$authUser) {
            return response()->json(['success' => false, 'message' => 'Tidak terautentikasi.'], 401);
        }

        $loginUser = LoginUser::find($authUser->id);
        if (!$loginUser) {
            return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan.'], 404);
        }

        $sekolah = Sekolah::find($loginUser->sekolah_id);
        $namaFolder = $sekolah ? Str::slug($sekolah->nama_sekolah) : 'sekolah-' . $loginUser->sekolah_id;

        // Hapus foto lama dari storage kalau ada, sebelum simpan yang baru
        if ($loginUser->foto && Storage::disk('public')->exists($loginUser->foto)) {
            Storage::disk('public')->delete($loginUser->foto);
        }

        $path = $request->file('foto')->store("foto-profil/{$namaFolder}", 'public');

        $loginUser->foto = $path;
        $loginUser->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui.',
            'foto_url' => Storage::url($path),
        ]);
    }

    public function getDataAkunGuru(Request $request)
    {
        try {
            $sekolahId = auth('api')->user()->sekolah_id ?? null;

            if (!$sekolahId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi tidak valid atau sekolah tidak ditemukan.'
                ], 401);
            }

            $guru = LoginUser::where('sekolah_id', $sekolahId)
                ->where('role', 'guru')
                ->select('id', 'nama_lengkap', 'username', 'password', 'is_active', 'is_use')
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
        $validator = Validator::make($request->all(), [
            'nama_lengkap'  => 'required|string|max:255',
            'username'      => 'required|string|max:255|unique:login_users,username',
            'password'      => 'required|string|min:4',
            'data_guru_id'  => 'required|exists:data_gurus,id',
        ], [
            'username.unique'      => 'Username ini sudah terdaftar di sistem.',
            'password.min'         => 'Password minimal harus 4 karakter.',
            'data_guru_id.required' => 'Silakan pilih nama guru terlebih dahulu.',
            'data_guru_id.exists'   => 'Data guru yang dipilih tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $sekolahId = auth('api')->user()->sekolah_id ?? null;

            if (!$sekolahId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi admin telah berakhir, silakan login kembali.'
                ], 401);
            }

            $paketLayanan = auth('api')->user()->paket_layanan ?? 'free';

            if (strtolower($paketLayanan) === 'free') {
                $jumlahGuruSoreIni = LoginUser::where('sekolah_id', $sekolahId)
                    ->where('role', 'guru')
                    ->count();

                if ($jumlahGuruSoreIni >= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Batas Paket Free Tercapai! Paket gratis hanya diperbolehkan memiliki maksimal 1 akun guru. Silakan upgrade paket untuk menambahkan akun tanpa batas.'
                    ], 422);
                }
            }

            // Pastikan data guru yang dipilih milik sekolah yang sama
            $dataGuru = DataGuru::where('id', $request->data_guru_id)
                ->where('sekolah_id', $sekolahId)
                ->first();

            if (!$dataGuru) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan di sekolah Anda.'
                ], 422);
            }

            // Pastikan data guru ini belum dikaitkan ke akun login lain
            if ($dataGuru->login_user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guru ini sudah memiliki akun login. Tidak bisa membuat akun ganda untuk guru yang sama.'
                ], 422);
            }

            // 1. Buat akun login guru
            $loginUser = LoginUser::create([
                'sekolah_id'   => $sekolahId,
                'username'     => strtolower($request->username),
                'password'     => Hash::make($request->password),
                'nama_lengkap' => $request->nama_lengkap,
                'no_whatsapp'  => null,
                'role'         => 'guru',
            ]);

            // 2. Kaitkan data guru yang dipilih ke akun login baru ini
            $dataGuru->update([
                'login_user_id' => $loginUser->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Akun guru berhasil didaftarkan dan terhubung dengan data guru.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tambahDataKelas(Request $request)
    {
        // Validasi Inputan Form
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:50',
            'tingkat'    => 'nullable|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'guru_id' => 'nullable|exists:data_gurus,id',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'tingkat.in'          => 'Tingkat yang dipilih tidak valid.',
            'guru_id.exists'      => 'Wali kelas yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Ambil sekolah_id dari admin yang sedang login
            $sekolahId = auth('api')->user()->sekolah_id ?? null;

            if (!$sekolahId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi admin telah berakhir, silakan login kembali.'
                ], 401);
            }

            // Cek duplikasi nama kelas dalam sekolah yang sama (sesuai unique constraint)
            $sudahAda = Kelas::where('sekolah_id', $sekolahId)
                ->where('nama_kelas', $request->nama_kelas)
                ->exists();

            if ($sudahAda) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama kelas "' . $request->nama_kelas . '" sudah terdaftar di sekolah ini.'
                ], 422);
            }

            Kelas::create([
                'sekolah_id' => $sekolahId,
                'nama_kelas' => $request->nama_kelas,
                'tingkat'    => $request->tingkat ?: null,
                'guru_id'    => $request->guru_id ?: null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data kelas berhasil ditambahkan.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllTransaksi(Request $request)
    {
        try {
            $sekolahId = auth('api')->user()->sekolah_id ?? null;

            if (!$sekolahId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi admin telah berakhir, silakan login kembali.'
                ], 401);
            }

            $transaksi = Transaksi::with([
                    'siswa:id,nama_siswa,kelas_id,sekolah_id',
                    'siswa.kelas:id,nama_kelas',
                    'petugas:id,nama_guru'
                ])
                ->whereHas('siswa', function ($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data transaksi sekolah Anda',
                'data'    => $transaksi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetStatusAkunGuru(Request $request, $id)
    {
        $admin = auth('api')->user();

        $loginUser = LoginUser::where('id', $id)
            ->where('sekolah_id', $admin->sekolah_id)
            ->whereHas('dataGuru')
            ->first();

        if (!$loginUser) {
            return response()->json([
                'success' => false,
                'message' => 'Akun guru tidak ditemukan atau bukan milik sekolah Anda.'
            ], 404);
        }

        $loginUser->is_active = !$loginUser->is_active;

        if (!$loginUser->is_active) {
            $loginUser->is_use = false;
        }

        $loginUser->save();

        $namaGuru = $loginUser->dataGuru->nama_guru ?? $loginUser->username;

        return response()->json([
            'success' => true,
            'message' => $loginUser->is_active
                ? "Akun {$namaGuru} berhasil diaktifkan."
                : "Akun {$namaGuru} berhasil dinonaktifkan.",
            'is_active' => $loginUser->is_active,
        ]);
    }

    public function resetSesiGuru($id)
    {
        $admin = auth('api')->user();

        $loginUser = LoginUser::where('id', $id)
            ->where('sekolah_id', $admin->sekolah_id)
            ->whereHas('dataGuru')
            ->first();

        if (!$loginUser) {
            return response()->json([
                'success' => false,
                'message' => 'Akun guru tidak ditemukan atau bukan milik sekolah Anda.'
            ], 404);
        }

        if (!$loginUser->is_use) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini sedang tidak dalam status login (sesi sudah tidak aktif).'
            ], 422);
        }

        $loginUser->is_use = false;
        $loginUser->save();

        $namaGuru = $loginUser->dataGuru->nama_lengkap ?? $loginUser->username;

        return response()->json([
            'success' => true,
            'message' => "Sesi login {$namaGuru} berhasil di-reset. Guru bisa login kembali dari device manapun.",
        ]);
    }
}