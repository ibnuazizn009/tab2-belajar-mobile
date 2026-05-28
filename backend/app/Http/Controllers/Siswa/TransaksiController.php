<?php

namespace App\Http\Controllers\siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
                'nis' => 'required|integer',
                'tipe' => 'required|string',
                'nominal' => 'required|integer'
            ]);

            $studentId = DB::table('tabel_transaksi')->insert([
                'nis' => $validatedData['nis'],
                // 'kdprofile' => 10,
                // 'statusenabled' => true,
                'tipe' => $validatedData['tipe'],
                'nominal' => $validatedData['nominal'],
                'created_at' => $updatedAt,
                'updated_at' => $updatedAt,
            ]);

            if ($studentId) {
                return $this->successResponse('Data Transaksi berhasil disimpan', $studentId);
            } else {
                return $this->failedResponse('Gagal menyimpan data Transaksi');
            }
        } catch (\Exception $e) {

            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
}
