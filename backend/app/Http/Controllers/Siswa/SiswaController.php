<?php

namespace App\Http\Controllers\siswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getDataSiswaPerKelas(Request $request)
    {
        $siswa = MasterSiswa::from('master_siswa as ms')
            ->join('kelas as kl', 'kl.id', '=', 'ms.kelas_id')
            ->where('ms.kelas_id', $request->kelasId)
            ->where('ms.isActive', 1)
            ->select('ms.nis', 'ms.nama', 'ms.saldo', 'kl.nama_kelas', 'ms.created_at', 'ms.updated_at')
            ->get();
        return response()->json([
            'data' => $siswa
        ]);
    }

    public function getDataTransaksiSiswaPerKelas(Request $request)
    {
        $kelasId = $request->input('kelasId') ?? $request->input('kelas_id');

        $siswa = MasterSiswa::from('master_siswa as ms')
            ->join('kelas as kl', 'kl.id', '=', 'ms.kelas_id')
            ->leftJoin('tabel_transaksi as tt', 'tt.nis', '=', 'ms.nis')
            ->where('ms.kelas_id', $kelasId)
            ->where('ms.isActive', 1)
            ->groupBy('ms.nis', 'ms.nama', 'ms.saldo', 'kl.nama_kelas')
            ->select(
                'ms.nis',
                'ms.nama',
                'ms.saldo', // Saldo berjalan saat ini di master_siswa
                'kl.nama_kelas',
                // Menghitung total nominal khusus yang tipenya 'setor'
                DB::raw("SUM(CASE WHEN tt.tipe = 'setor' THEN tt.nominal ELSE 0 END) as total_nominal_setor")
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => $siswa
        ]);
    }

    public function getDataTransaksiTanggal(Request $request)
    {
        $kelasId  = $request->input('kelasId') ?? $request->input('kelas_id');
        $tglAwal  = $request->input('tgl_awal');  
        $tglAkhir = $request->input('tgl_akhir'); 

        $Transaksi = MasterSiswa::from('master_siswa as ms')
            ->join('kelas as kl', 'kl.id', '=', 'ms.kelas_id')
            ->join('tabel_transaksi as tt', function($join) use ($tglAwal, $tglAkhir) {
                $join->on('tt.nis', '=', 'ms.nis');
                if ($tglAwal && $tglAkhir) {
                    $join->whereBetween('tt.created_at', [$tglAwal, $tglAkhir]);
                }
            })
            ->where('ms.kelas_id', $kelasId)
            ->where('ms.isActive', 1)
            ->groupBy('ms.nis', 'ms.nama', 'ms.saldo', 'kl.nama_kelas')
            ->select(
                'ms.nis',
                'ms.nama',
                'ms.saldo',
                'kl.nama_kelas',
                DB::raw("MAX(tt.created_at) as tgl_transaksi_terakhir"),
                DB::raw("COUNT(tt.id) as jumlah_transaksi"), // tambah ini
            )
            ->get();

        $totalTransaksi = $Transaksi->sum('jumlah_transaksi');
        return response()->json([
            'success' => true,
            'data' => $Transaksi,
            'total_transaksi' => $totalTransaksi
        ]);
    }
}
