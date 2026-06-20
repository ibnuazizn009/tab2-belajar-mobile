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
        $this->middleware('jwt.cookie', ['except' => ['getAllKota', 'getSekolahByKota']]);
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

    public function getAllKelasAdmin(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'sekolah_id' => 'required|exists:sekolah,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $daftarKelas = Kelas::leftJoin('data_gurus', 'kelas.guru_id', '=', 'data_gurus.id')
                            ->where('kelas.sekolah_id', $request->sekolah_id)
                            ->orderBy('kelas.tingkat', 'asc')
                            ->orderBy('kelas.nama_kelas', 'asc')
                            ->select('kelas.*', 'data_gurus.nama_guru')
                            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data kelas internal sekolah Anda',
            'data'    => $daftarKelas
        ], 200);
    }
}