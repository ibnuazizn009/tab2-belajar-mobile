<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Imports\SiswaImport; 
use App\Exports\TemplateSiswaExport;
use App\Models\Transaksi;
use App\Helpers\LicenseChecker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function getDataKelas(Request $request)
    {
        try {
            $sekolahId = (int) Auth::user()->sekolah_id;
            $kelas = Kelas::where('id', (int) $request->kelasId)
                          ->where('sekolah_id', $sekolahId)
                          ->first();

                          
            if (!$kelas) {
                return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan atau bukan hak akses Anda.'], 404);
            }

            return response()->json([
                'success'    => true,
                'nama_kelas' => $kelas->nama_kelas,
                'tingkat'    => $kelas->tingkat,
                'created_at' => $kelas->created_at,
                'updated_at' => $kelas->updated_at
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error getDataKelas: ' . $e->getMessage()], 500);
        }
    }

    public function getDataSiswaPerKelas(Request $request)
    {
        try {
            $sekolahId = Auth::user()->sekolah_id;

            $siswa = Siswa::with('kelas')
                ->where('sekolah_id', $sekolahId)
                ->where('kelas_id', $request->kelasId)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'         => $item->id, 
                        'nis'        => $item->nis,
                        'nama_siswa' => $item->nama_siswa,
                        'saldo'      => $item->saldo,
                        'nama_kelas' => $item->kelas->nama_kelas ?? '-',
                        'is_active'  => $item->is_active, 
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                });

            return response()->json(['success' => true, 'data' => $siswa]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error getDataSiswaPerKelas: ' . $e->getMessage()], 500);
        }
    }

    public function getDataTransaksiSiswaPerKelas(Request $request)
    {
        try {
            $sekolahId = Auth::user()->sekolah_id;
            $kelasId   = $request->input('kelasId') ?? $request->input('kelas_id');
            $tglAwal   = $request->input('tgl_awal');  
            $tglAkhir  = $request->input('tgl_akhir');

            $totalTabunganBersih = Transaksi::whereHas('siswa', function($query) use ($sekolahId, $kelasId) {
                    $query->where('sekolah_id', $sekolahId)->where('kelas_id', $kelasId)->where('is_active', 1);
                })
                ->when($tglAwal && $tglAkhir, function($query) use ($tglAwal, $tglAkhir) {
                    return $query->whereBetween('created_at', [$tglAwal, $tglAkhir]);
                })
                ->select(DB::raw("
                    SUM(CASE WHEN tipe = 'setor' THEN nominal ELSE 0 END) - 
                    SUM(CASE WHEN tipe = 'tarik' THEN nominal ELSE 0 END) as total_bersih
                "))
                ->first()
                ->total_bersih ?? 0; 
            
            $totalSiswa = Siswa::where('sekolah_id', $sekolahId)
                ->where('kelas_id', $kelasId)
                ->where('is_active', 1)
                ->count();

            // Menggunakan Carbon::now('Asia/Jakarta')->startOfDay() agar presisi dengan timezone Indonesia
            $transaksiHariIni = Transaksi::whereHas('siswa', function($query) use ($sekolahId, $kelasId) {
                    $query->where('sekolah_id', $sekolahId)->where('kelas_id', $kelasId);
                })
                ->whereDate('created_at', Carbon::now('Asia/Jakarta')->startOfDay())
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'totalTabungan'    => (int)$totalTabunganBersih,
                    'totalSiswa'       => $totalSiswa,
                    'transaksiHariIni' => $transaksiHariIni
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error getDataTransaksiSiswaPerKelas: ' . $e->getMessage()], 500);
        }
    }

    public function getDataTransaksiTanggal(Request $request)
    {
        try {
            $sekolahId = Auth::user()->sekolah_id;
            $kelasId   = $request->input('kelasId') ?? $request->input('kelas_id');
            $tglAwal   = $request->input('tgl_awal');  
            $tglAkhir  = $request->input('tgl_akhir'); 

            $siswaData = Siswa::with('kelas')
                ->where('sekolah_id', $sekolahId)
                ->where('kelas_id', $kelasId)
                ->where('is_active', 1)
                ->get()
                ->map(function($siswa) use ($tglAwal, $tglAkhir) {
                    $queryTransaksi = $siswa->transaksis();
                    
                    if ($tglAwal && $tglAkhir) {
                        $queryTransaksi->whereBetween('created_at', [$tglAwal, $tglAkhir]);
                    }

                    return [
                        'id'                     => $siswa->id,
                        'nis'                    => $siswa->nis,
                        'nama_siswa'             => $siswa->nama_siswa,
                        'saldo'                  => $siswa->saldo,
                        'nama_kelas'             => $siswa->kelas->nama_kelas ?? '-',
                        'tgl_transaksi_terakhir' => $queryTransaksi->max('created_at'),
                        'jumlah_transaksi'       => $queryTransaksi->count()
                    ];
                });

            return response()->json([
                'success'         => true,
                'data'            => $siswaData,
                'total_transaksi' => $siswaData->sum('jumlah_transaksi')
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error getDataTransaksiTanggal: ' . $e->getMessage()], 500);
        }
    }

    public function postTambahSiswa(Request $request)
    {
        try {
            $sekolahId = Auth::user()->sekolah_id;

            // 🎯 CEK KUOTA LISENSI
            if (!LicenseChecker::canAddSiswa($sekolahId)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Batas maksimal kuota 10 siswa untuk paket Free telah tercapai. Silakan upgrade ke paket Middle atau Golden!'
                ], 403);
            }
            
            $request->validate([
                'nis'            => 'required|string',
                'nama_siswa'     => 'required|string|max:150',
                'kelas_id'       => 'required|exists:kelas,id',
                'no_wa_orang_tua'=> 'nullable|string|max:20',
                'aktif_menabung' => 'required|in:0,1',
            ]);

            // Cek NIS unik di lingkup sekolah yang sama
            $isExist = Siswa::where('sekolah_id', $sekolahId)
                            ->where('nis', $request->nis)
                            ->exists();

            if ($isExist) {
                return response()->json(['success' => false, 'message' => 'Nomor NIS ini sudah terdaftar di sekolah Anda.'], 400);
            }

            $siswa = Siswa::create([
                'sekolah_id'      => $sekolahId, 
                'kelas_id'        => $request->kelas_id,
                'nis'             => $request->nis,
                'nama_siswa'      => $request->nama_siswa,
                'no_wa_orang_tua' => $request->no_wa_orang_tua,
                'saldo'           => 0, 
                'is_active'       => $request->aktif_menabung,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Siswa berhasil disimpan',
                'data'    => $siswa
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error postTambahSiswa: ' . $e->getMessage()], 500);
        }
    }

    public function getLaporanTransaksiSiswa(Request $request)
    {
        try {
            $request->validate([
                'nis' => 'required|exists:siswa,nis'
            ]);

            $sekolahId = Auth::user()->sekolah_id;

            $transaksi = Transaksi::whereHas('siswa', function($query) use ($sekolahId, $request) {
                $query->where('sekolah_id', $sekolahId)
                      ->where('nis', $request->nis);
            })
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'data'    => $transaksi,
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error getLaporanTransaksiSiswa: ' . $e->getMessage()], 500);
        }
    }

     /**
     * Mengambil laporan keuangan ringkas seluruh siswa berdasarkan kelasId
     */
    public function getLaporanKeuanganSiswa(Request $request)
    {
        $kelasId = $request->query('kelasId');

        if (!$kelasId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter kelasId wajib diisi.'
            ], 400);
        }

        try {
            $dataLaporan = DB::table('siswa')
                ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->select(
                    'siswa.nis',
                    'siswa.nama_siswa',
                    'siswa.saldo',
                    'kelas.nama_kelas'
                )
                ->where('siswa.kelas_id', $kelasId)
                ->orderBy('siswa.nama_siswa', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memuat laporan keuangan siswa.',
                'data'    => $dataLaporan
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server saat memuat data.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new TemplateSiswaExport, 'Template_Siswa.xlsx');
    }

    public function importExcel(Request $request)
    {
        $sekolahId = Auth::user()->sekolah_id;
        $sekolah = \App\Models\Sekolah::find($sekolahId);

        // Validasi paket layanan
        if ($sekolah->paket_layanan !== 'golden') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini hanya tersedia untuk paket Golden.'
            ], 403);
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            Excel::import(new SiswaImport($sekolahId), $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Import berhasil!'
            ]);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = collect($e->failures())->map(function($f) {
                return "Baris {$f->row()}: " . implode(', ', $f->errors());
            });
        
            return response()->json([
                'success' => false,
                'message' => 'Gagal import: data tidak valid.',
                'errors'  => $failures
            ], 422);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal import: ' . $e->getMessage()
            ], 500);
        }
    }
}