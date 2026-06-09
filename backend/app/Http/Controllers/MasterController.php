<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Kota;
use App\Models\Sekolah;

class MasterController extends Controller
{
    public function getSekolahByKota(Request $request)
    {
        // Validasi parameter kota_id wajib dikirim
        $request->validate([
            'kota_id' => 'required|exists:kota,id'
        ]);

        $sekolah = Sekolah::where('kota_id', $request->kota_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data sekolah berdasarkan kota',
            'data'    => $sekolah
        ], 200);
    }

    public function getAllKota()
    {
        $kota = Kota::where('provinsi', 'Jawa Barat')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data kota',
            'data'    => $kota
        ], 200);
    }

    public function getAllKelas()
    {
        $daftarKelas = Kelas::all();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil semua data kelas',
            'data'    => $daftarKelas
        ], 200);
    }
}
