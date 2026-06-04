<?php

namespace App\Http\Controllers\siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterSiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App;

class TransaksiController extends Controller
{
    use  App\Traits\ApiResponse\ApiResponse;
    use App\Traits\LogingSystems\LogingSystems;

    public function postTransaksiSiswa(Request $request)
    {
        $updatedAt = Carbon::now('Asia/Jakarta');

        try {
            $validatedData = $request->validate([
                'nis'     => 'required|integer',
                'tipe'    => 'required|in:setor,tarik', // validasi lebih ketat
                'nominal' => 'required|integer|min:1',  // nominal minimal 1
            ]);

            $studentSaldo = DB::table('master_siswa')
                ->where('nis', $validatedData['nis'])
                ->first();

            if ($studentSaldo == null) {
                return $this->resourceNotFoundResponse('Data siswa tidak ditemukan.');
            }

            if ($validatedData['tipe'] === 'tarik' && $studentSaldo->saldo < $validatedData['nominal']) {
                return $this->failedResponse('Saldo tidak mencukupi.');
            }

            $saldoBaru = $validatedData['tipe'] === 'setor'
                ? $studentSaldo->saldo + $validatedData['nominal']
                : $studentSaldo->saldo - $validatedData['nominal'];

            $studentId = DB::table('tabel_transaksi')->insert([
                'nis'        => $validatedData['nis'],
                'tipe'       => $validatedData['tipe'],
                'nominal'    => $validatedData['nominal'],
                'created_at' => $updatedAt,
                'updated_at' => $updatedAt,
            ]);

            DB::table('master_siswa')
                ->where('nis', $validatedData['nis'])
                ->update(['saldo' => $saldoBaru]);

            if ($studentId) {
                return $this->successResponse('Data Transaksi berhasil disimpan', $studentId);
            } else {
                return $this->failedResponse('Gagal menyimpan data Transaksi');
            }

        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function getRiwayatTransaksiSiswa(Request $request)
    {
        $kelasId  = $request->input('kelasId') ?? $request->input('kelas_id');
        $tglAwal  = $request->input('tgl_awal');  
        $tglAkhir = $request->input('tgl_akhir'); 

        $Transaksi = MasterSiswa::from('master_siswa as ms')
            ->join('kelas as kl', 'kl.id', '=', 'ms.kelas_id')
            ->join('tabel_transaksi as tt', function($join) use ($tglAwal, $tglAkhir) {
                $join->on('tt.nis', '=', 'ms.nis');
                
                if ($tglAwal && $tglAkhir) {
                    $dateStart = date('Y-m-d', strtotime($tglAwal));
                    $dateEnd   = date('Y-m-d', strtotime($tglAkhir));
            
                    $fullStart = $dateStart . ' 00:00:00';
                    $fullEnd   = $dateEnd . ' 23:59:59';
            
                    $join->whereRaw("tt.created_at BETWEEN ? AND ?", [$fullStart, $fullEnd]);
                }
            })
            ->where('ms.kelas_id', $kelasId)
            ->where('ms.isActive', 1)
            ->select(
                'ms.nis',
                'ms.nama',
                'ms.saldo',
                'kl.nama_kelas',
                DB::raw("DATE_FORMAT(tt.created_at, '%Y-%m-%d %H:%i:%s') as tanggal_transaksi"),
                'tt.nominal',
                'tt.tipe'
            )
            ->orderBy('tt.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $Transaksi,
        ]);
    }
}
