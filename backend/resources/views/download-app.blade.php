@extends('layouts.admin')
@section('title', 'Download App Guru')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Download Aplikasi Guru</h2>
        <p class="text-xs text-slate-500 mt-1">Aplikasi mobile untuk guru mencatat dan memantau tabungan siswa langsung dari HP.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white border border-slate-100 rounded-2xl shadow-xs p-6 lg:col-span-2">
            <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-50">
                <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                <h3 class="text-sm font-bold text-slate-900">Tentang Aplikasi</h3>
            </div>

            <div class="space-y-4 text-xs text-slate-600 leading-relaxed">
                <p>
                    E-Tabungan App adalah aplikasi mobile khusus untuk guru, dirancang agar pencatatan
                    setoran dan penarikan tabungan siswa bisa dilakukan langsung dari kelas tanpa perlu
                    membuka komputer atau laptop.
                </p>

                <div>
                    <p class="font-bold text-slate-800 mb-2">Fitur utama:</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-emerald-500 text-[10px] mt-0.5"></i>
                            <span>Catat setoran dan penarikan tabungan siswa secara real-time.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-emerald-500 text-[10px] mt-0.5"></i>
                            <span>Lihat riwayat transaksi dan saldo tabungan per siswa di kelas yang diampu.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-emerald-500 text-[10px] mt-0.5"></i>
                            <span>Login aman dengan akun yang sudah didaftarkan oleh Admin Sekolah.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-emerald-500 text-[10px] mt-0.5"></i>
                            <span>Notifikasi dan ringkasan tabungan harian per kelas.</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3.5 flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xs mt-0.5 shrink-0"></i>
                    <p class="text-[11px] text-amber-700 leading-relaxed">
                        File APK ini didistribusikan langsung (di luar Play Store). Pastikan mengunduh
                        hanya dari halaman resmi ini, dan aktifkan opsi "Izinkan instalasi dari sumber
                        tidak dikenal" di HP Android saat instalasi.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl shadow-xs p-6 h-fit">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-tr from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-slate-900">E-Tabungan App</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Untuk Guru &middot; Android</p>
                </div>

                <div class="flex items-center gap-2 text-[10px] font-bold">
                    <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-lg border border-slate-100">v1.0</span>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg">Android</span>
                </div>

                <a href="https://etabungan-tab2one.hopto.org/apps/E-TabunganV1.0.apk" download
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-down-to-line"></i>
                    Download APK
                </a>

                <p class="text-[10px] text-slate-400 leading-relaxed">
                    Dengan mengunduh, Anda menyetujui penggunaan aplikasi ini sesuai kebijakan sekolah Anda.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection