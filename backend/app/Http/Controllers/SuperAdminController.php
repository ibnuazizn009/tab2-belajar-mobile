<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sekolah;
use App\Models\LoginUser;
use App\Models\JenjangSekolah;
use App\Models\Kota;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    public function showRegisterForm()
    {
        $jenjangs = JenjangSekolah::orderBy('id', 'asc')->get();

        $kotas = Kota::orderBy('nama_kota', 'asc')->get();

        $paket_terpilih = request()->query('paket', 'BRONZE');

        return view('auth.register', compact('jenjangs', 'kotas', 'paket_terpilih'));
    }

    public function registerSekolahBaru(Request $request)
    {
        try {
            $request->validate([
                'npsn'          => 'required|string|size:8|unique:sekolah,npsn',
                'nama_sekolah'  => 'required|string|max:255',
                'email_sekolah' => 'required|email|max:255',
                'jenjang_id'    => 'required|numeric', 
                'status'        => 'required|in:NEGERI,SWASTA',
                'alamat'        => 'nullable|string',
                'kota_id'       => 'required|numeric', 
                'paket_layanan' => 'required|in:BRONZE,SILVER,GOLDEN', 
                
                'nama_lengkap'  => 'required|string|max:255',
                'no_whatsapp'   => 'required|string|max:20',
                'username'      => 'required|string|min:4|unique:login_users,username',
                'password'      => 'required|string|min:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal di server.',
                'errors'  => $e->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            if ($request->paket_layanan === 'BRONZE') {
                $isPremium = 0; 
                $expiresAt = Carbon::now()->addDays(7); // Masa trial BRONZE 7 hari
            } elseif ($request->paket_layanan === 'SILVER') {
                $isPremium = 1; 
                $expiresAt = Carbon::now()->addDays(30); // Masa aktif SILVER 30 hari
            } else { // GOLDEN
                $isPremium = 1; 
                $expiresAt = Carbon::now()->addDays(30); // Masa aktif GOLDEN 30 hari
            }

            $sekolah = Sekolah::create([
                'npsn'               => $request->npsn,
                'nama_sekolah'       => strtoupper($request->nama_sekolah),
                'email_sekolah'      => strtolower($request->email_sekolah),
                'jenjang_id'         => $request->jenjang_id,
                'status'             => $request->status,
                'alamat'             => $request->alamat,
                'kota_id'            => $request->kota_id,
                'is_premium'         => $isPremium,
                'paket_layanan'      => $request->paket_layanan,
                'premium_expires_at' => $expiresAt, 
            ]);

            $admin = LoginUser::create([
                'sekolah_id'   => $sekolah->id,
                'username'     => strtolower($request->username),
                'password'     => Hash::make($request->password),
                'nama_lengkap' => $request->nama_lengkap,
                'no_whatsapp'  => $request->no_whatsapp,
                'role'         => 'admin_sekolah',
            ]);

            // =================================================================
            // 🌟 AWAL LOGIKA INTEGRASI XENDIT PAYMENT GATEWAY
            // =================================================================
            $redirectUrl = null;
            $harga = 0;

            if ($request->paket_layanan === 'SILVER') {
                $harga = 150000;
            } elseif ($request->paket_layanan === 'GOLDEN') {
                $harga = 350000;
            }

            // Jika memilih paket berbayar, buatkan invoice di Xendit
            if ($harga > 0) {
                // Inisialisasi API Key dari file .env
                Configuration::setXenditKey(config('services.xendit.secret_key'));
                $apiInstance = new InvoiceApi();

                $createInvoiceRequest = new CreateInvoiceRequest([
                    'external_id' => 'REG-' . $sekolah->id . '-' . time(),
                    'amount' => $harga,
                    'payer_email' => $request->email_sekolah, 
                    'description' => 'Aktivasi Layanan E-Tabungan Paket ' . $request->paket_layanan . ' - ' . $sekolah->nama_sekolah,
                    'success_redirect_url' => route('login'), // Arahkan kembali ke login setelah sukses bayar
                ]);

                // Kirim request ke server Xendit
                $invoice = $apiInstance->createInvoice($createInvoiceRequest);
                
                // Ambil link pembayaran aman dari Xendit
                $redirectUrl = $invoice['invoice_url'];
            }
            // =================================================================
            // 🌟 AKHIR LOGIKA INTEGRASI XENDIT
            // =================================================================

            DB::commit();

            $responsePayload = [
                'status'  => 'success',
                'message' => $harga > 0 
                    ? 'Pendaftaran berhasil! Mengalihkan Anda ke halaman pembayaran Xendit...' 
                    : 'Sekolah dan Akun Admin berhasil didaftarkan!',
                'data'    => [
                    'nama_sekolah'   => $sekolah->nama_sekolah,
                    'is_premium'     => $sekolah->is_premium,
                    'username'       => $admin->username,
                    'berlaku_sampai' => $sekolah->premium_expires_at->format('Y-m-d H:i:s')
                ]
            ];

            if ($redirectUrl) {
                $responsePayload['redirect_url'] = $redirectUrl;
            }

            return response()->json($responsePayload, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mendaftarkan sekolah, terjadi kesalahan sistem.',
                'error'   => $e->getMessage() 
            ], 500);
        }
    }

    public function handleXenditCallback(Request $request)
    {
        // 1. Validasi Keamanan: Pastikan request benar-benar datang dari Xendit
        $xenditXCallbackToken = $request->header('x-callback-token');

        if ($xenditXCallbackToken !== config('services.xendit.callback_token')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token callback tidak valid / Ilegal!'
            ], 403);
        }

        // 2. Ambil data payload dari Xendit
        $data = $request->all();
        
        $externalId = $data['external_id']; 
        $status     = $data['status'];     

        // 3. Jika statusnya PAID atau SETTLED, update database Sekolah
        if ($status === 'PAID' || $status === 'SETTLED') {
            
            // Pecah external_id untuk mengambil ID Sekolah (REG-{id_sekolah}-{time})
            $parts = explode('-', $externalId);
            $sekolahId = $parts[1] ?? null;

            if ($sekolahId) {
                $sekolah = Sekolah::find($sekolahId);

                if ($sekolah) {
                    // Set premium aktif dan hitung mundur 30 hari
                    $sekolah->update([
                        'is_premium'         => 1,
                        'premium_expires_at' => Carbon::now()->addDays(30),
                    ]);

                    return response()->json([
                        'status'  => 'success',
                        'message' => 'Database Sekolah berhasil diperbarui menjadi Aktif/Premium!'
                    ], 200);
                }
            }
        }

        return response()->json(['status' => 'ignored'], 200);
    }
}