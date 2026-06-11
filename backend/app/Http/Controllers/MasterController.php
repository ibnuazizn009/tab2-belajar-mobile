<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Kota;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    /**
     * Konstruktor untuk mengamankan data internal sekolah,
     * namun membebaskan pencarian kota & sekolah untuk kebutuhan registrasi publik.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAllKota', 'getSekolahByKota']]);
    }

    public function getAllKota()
    {
        $kota = Kota::where('provinsi', 'Jawa Barat')
                    ->orderBy('nama_kota', 'asc')
                    ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data kota',
            'data'    => $kota
        ], 200);
    }

    public function getSekolahByKota(Request $request)
    {
        $request->validate([
            'kota_id' => 'required|exists:kota,id'
        ]);

        $sekolah = Sekolah::with('jenjang')
            ->where('kota_id', $request->kota_id)
            ->orderBy('nama_sekolah', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data sekolah berdasarkan kota',
            'data'    => $sekolah
        ], 200);
    }

    public function getAllKelas()
    {
        // Fungsi ini wajib menggunakan token login JWT (Guard API)
        $user = Auth::user();

        if (!$user->sekolah_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Akun Anda tidak terikat dengan sekolah mana pun.',
            ], 403);
        }

        $daftarKelas = Kelas::where('sekolah_id', $user->sekolah_id)
                            ->orderBy('tingkat', 'asc')
                            ->orderBy('nama_kelas', 'asc')
                            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data kelas internal sekolah Anda',
            'data'    => $daftarKelas
        ], 200);
    }
}