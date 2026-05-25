<?php

namespace App\Http\Controllers\siswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function getDataKelas(Request $request)
    {
        $kelas = Kelas::where('id', $request->kelasId)->first();
        return response()->json([
            'nama_kelas' => $kelas->nama_kelas,
            'tingkat' => $kelas->tingkat,
            'created_at' => $kelas->created_at,
            'updated_at' => $kelas->updated_at
        ]);
    }

    public function getDataSiswa(Request $request)
    {
        $siswa = MasterSiswa::from('master_siswa as ms')
            ->join('kelas as kl', 'kl.id', '=', 'ms.kelas_id')
            ->where('ms.kelas_id', $request->kelasId)
            ->select('ms.nama', 'ms.saldo', 'kl.nama_kelas', 'ms.created_at', 'ms.updated_at')
            ->get();
        return response()->json([
            'data' => $siswa
        ]);
    }
}
