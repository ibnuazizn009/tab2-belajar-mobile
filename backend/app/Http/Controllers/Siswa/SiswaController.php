<?php

namespace App\Http\Controllers\siswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\TabelTransaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App;

class SiswaController extends Controller
{
    use  App\Traits\ApiResponse\ApiResponse;
    use App\Traits\LogingSystems\LogingSystems;
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
            // ->where('ms.isActive', 1)
            ->select('ms.nis', 'ms.nama', 'ms.saldo', 'kl.nama_kelas', 'ms.isActive', 'ms.created_at', 'ms.updated_at')
            ->get();
        return response()->json([
            'data' => $siswa
        ]);
    }

    public function getDataTransaksiSiswaPerKelas(Request $request)
    {
        $kelasId = $request->input('kelasId') ?? $request->input('kelas_id');
        $tglAwal  = $request->input('tgl_awal');  
        $tglAkhir = $request->input('tgl_akhir');

        $totalTabunganBersih = MasterSiswa::from('master_siswa as ms')
            ->join('tabel_transaksi as tt', 'tt.nis', '=', 'ms.nis')
            ->where('ms.kelas_id', $kelasId)
            ->where('ms.isActive', 1)
            ->when($tglAwal && $tglAkhir, function($query) use ($tglAwal, $tglAkhir) {
                return $query->whereBetween('tt.created_at', [$tglAwal, $tglAkhir]);
            })
            ->select(DB::raw("
                SUM(CASE WHEN tt.tipe = 'setor' THEN tt.nominal ELSE 0 END) - 
                SUM(CASE WHEN tt.tipe = 'tarik' THEN tt.nominal ELSE 0 END) as total_bersih
            "))
            ->first()
            ->total_bersih ?? 0; 
        
        $totalSiswa = MasterSiswa::where('kelas_id', $kelasId)->where('isActive', 1)->count();

        $transaksiHariIni = MasterSiswa::from('master_siswa as ms')
            ->join('tabel_transaksi as tt', 'tt.nis', '=', 'ms.nis')
            ->where('ms.kelas_id', $kelasId)
            ->whereDate('tt.created_at', \Carbon\Carbon::today())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'totalTabungan' => (int)$totalTabunganBersih,
                'totalSiswa' => $totalSiswa,
                'transaksiHariIni' => $transaksiHariIni
            ]
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

    public function postTambahSiswa(Request $request)
    {
        $updatedAt = Carbon::now('Asia/Jakarta');

        try {
            $validatedData = $request->validate([
                'nis'     => 'required|integer',
                'nama'    => 'required|string',
                'kelas_id' => 'required|integer|min:1',
                'saldo' => 'required|integer|min:0',
                'aktif_menabung' => 'required|integer|in:0,1',

            ]);

            $student = DB::table('master_siswa')
                ->where('nis', $validatedData['nis'])
                ->first();

            if ($student) {
                return $this->resourceNotFoundResponse('Data siswa sudah ada.');
            }

            $studentId = DB::table('master_siswa')->insert([
                'nis'        => $validatedData['nis'],
                'nama'       => $validatedData['nama'],
                'kelas_id'   => $validatedData['kelas_id'],
                'saldo'      => $validatedData['saldo'],
                'isActive'   => $validatedData['aktif_menabung'],
                'created_at' => $updatedAt,
                'updated_at' => $updatedAt,
            ]);

            if ($studentId) {
                return $this->successResponse('Data Siswa berhasil disimpan', $studentId);
            } else {
                return $this->failedResponse('Gagal menyimpan data Siswa');
            }

        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function getLaporanKeuanganSiswa(Request $request)
    {
        $siswa = MasterSiswa::from('master_siswa as ms')
            ->join('kelas as kl', 'kl.id', '=', 'ms.kelas_id')
            ->where('ms.kelas_id', $request->kelasId)
            // ->where('ms.isActive', 1)
            ->select('ms.nis', 'ms.nama', 'ms.saldo', 'kl.nama_kelas', 'ms.isActive', 'ms.created_at', 'ms.updated_at')
            ->get();
        return response()->json([
            'data' => $siswa
        ]);
    }

    public function getLaporanTransaksiSiswa(Request $request)
    {
        $nis = $request->input('nis');

        $transaksi = TabelTransaksi::where('nis', $nis)
            ->orderBy('created_at', 'desc')
            ->select(
                'id',
                'nis',
                'tipe',    
                'nominal',
                'created_at'
            )
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $transaksi,
        ]);
    }
}
