<?php

namespace App\Http\Controllers\siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa; 
use App\Models\Transaksi; 
use App\Models\WhatsappLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function postTransaksiSiswa(Request $request)
    {
        $request->validate([
            'nis'       => 'required|exists:siswa,nis',
            'tipe'      => 'required|in:setor,tarik',
            'nominal'   => 'required|integer|min:100',
            'keterangan'=> 'nullable|string|max:255'
        ]);

        $sekolahId = Auth::user()->sekolah_id;
        $petugasId = Auth::user()->id;
        $waktuSekarang = Carbon::now('Asia/Jakarta');

        DB::beginTransaction();

        try {
            $siswa = Siswa::where('nis', $request->nis)
                                ->where('sekolah_id', $sekolahId)
                                ->lockForUpdate() // Mencegah manipulasi saldo ganda dalam waktu bersamaan
                                ->first();

            if (!$siswa) {
                return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan di sekolah Anda.'], 404);
            }

            if (!$siswa->is_active) { 
                return response()->json(['success' => false, 'message' => 'Siswa tersebut berstatus nonaktif, tidak bisa melakukan transaksi.'], 400);
            }

            $saldoAwal  = $siswa->saldo;
            $nominal    = $request->nominal;

            if ($request->tipe === 'tarik' && $saldoAwal < $nominal) {
                return response()->json(['success' => false, 'message' => 'Transaksi gagal. Saldo siswa tidak mencukupi.'], 400);
            }

            $saldoAkhir = $request->tipe === 'setor' ? $saldoAwal + $nominal : $saldoAwal - $nominal;

            $transaksi = Transaksi::create([
                'siswa_id'    => $siswa->id,
                'user_id'     => $petugasId,
                'tipe'        => $request->tipe,
                'nominal'     => $nominal,
                'saldo_awal'  => $saldoAwal,
                'saldo_akhir' => $saldoAkhir,
                'keterangan'  => $request->keterangan,
                'created_at'  => $waktuSekarang,
            ]);

            $siswa->update(['saldo' => $saldoAkhir]);

            // 7. OTOMATISASI WHATSAPP LOG: Buat draf pesan jika nomor orang tua diisi
            if ($siswa->no_wa_orang_tua) {
                $formatNominal = number_format($nominal, 0, ',', '.');
                $formatTotal   = number_format($saldoAkhir, 0, ',', '.');
                $aksi          = $request->tipe === 'setor' ? 'PENYETORAN' : 'PENARIKAN';
                
                $pesanWa = "Yth. Orang Tua/Wali dari *{$siswa->nama_siswa}*,\n\n" . 
                           "Menginfokan laporan transaksi tabungan sekolah pada *{$waktuSekarang->format('d-m-Y H:i')}*:\n" .
                           "• Jenis Transaksi: *{$aksi}*\n" .
                           "• Nominal: *Rp {$formatNominal}*\n" .
                           "• Sisa Saldo: *Rp {$formatTotal}*\n\n" .
                           "Terima kasih atas kepercayaan Anda.\n_Pesan ini dikirim otomatis oleh Sistem E-Tabungan Sekolah._";

                WhatsappLog::create([
                    'no_tujuan' => $siswa->no_wa_orang_tua,
                    'pesan'     => $pesanWa,
                    'status'    => 'pending' // Siap dieksekusi oleh background worker WA gateway
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Transaksi berhasil disimpan dan saldo diperbarui.',
                'data'    => [
                    'transaksi_id' => $transaksi->id,
                    'nama_siswa'   => $siswa->nama_siswa,
                    'tipe'         => $transaksi->tipe,
                    'nominal'      => $transaksi->nominal,
                    'saldo_akhir'  => $siswa->saldo
                ]
            ], 201);

        } catch (\Exception $e) {
            // Jika terjadi kegagalan sistem di tengah jalan, batalkan semua manipulasi uang
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses transaksi: ' . $e->getMessage()], 500);
        }
    }

    public function getRiwayatTransaksiSiswa(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        $sekolahId = Auth::user()->sekolah_id;
        $tglAwal   = $request->input('tgl_awal');  
        $tglAkhir  = $request->input('tgl_akhir'); 

        $riwayat = Transaksi::with(['siswa', 'siswa.kelas']) 
            ->whereHas('siswa', function($query) use ($sekolahId, $request) {
                $query->where('sekolah_id', $sekolahId)
                      ->where('kelas_id', $request->kelas_id);
            })
            ->when($tglAwal && $tglAkhir, function($query) use ($tglAwal, $tglAkhir) {
                $fullStart = Carbon::parse($tglAwal)->startOfDay();
                $fullEnd   = Carbon::parse($tglAkhir)->endOfDay();
                return $query->whereBetween('created_at', [$fullStart, $fullEnd]);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'nis'               => $item->siswa->nis ?? '-',
                    'nama_siswa'        => $item->siswa->nama_siswa ?? '-',
                    'saldo_saat_ini'    => $item->siswa->saldo ?? 0,
                    'nama_kelas'        => $item->siswa->kelas->nama_kelas ?? '-',
                    'tanggal_transaksi' => $item->created_at->format('Y-m-d H:i:s'),
                    'nominal'           => $item->nominal,
                    'tipe'              => $item->tipe,
                    'keterangan'        => $item->keterangan
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $riwayat,
        ]);
    }
}