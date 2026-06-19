@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6 opacity-0 transition-opacity duration-500 ease-in-out" id="dashboard-wrapper">
    <div id="banner-expired" class="hidden mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-pulse">   
        <div class="flex items-start gap-3.5 min-w-0 flex-1">
            <div class="p-2 bg-red-100 text-red-600 rounded-xl text-base shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h5 id="alert-title" class="text-sm font-bold text-red-900">Masa Aktif Paket Habis!</h5>
                <p id="alert-desc" class="text-[12px] text-red-600 mt-0.5 leading-relaxed">Masa aktif layanan sekolah Anda telah berakhir (0 Hari Tersisa). Fitur transaksi saat ini dibekukan sementara. Silakan lakukan pembaruan atau perpanjangan paket layanan segera.</p>
            </div>
        </div>

        <div class="w-full sm:w-auto shrink-0 pl-11 sm:pl-0">
            <a href="/admin/billing" id="btn-update-paket" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-md shadow-red-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i class="fa-solid fa-credit-card text-[10px]"></i>
                <span>Perbarui Paket</span>
            </a>
        </div>
    </div>
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Dashboard Sekolah</h2>
        <p class="text-xs text-slate-500 mt-1">Monitoring real-time aktivitas & sistem E-Tabungan.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white border border-slate-100 p-5 rounded-2xl shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between group">
            <div class="flex items-center gap-4 min-w-0">
                <div class="p-3.5 bg-blue-50/80 text-blue-600 rounded-xl text-lg group-hover:scale-110 transition duration-300 shrink-0">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Siswa</p>
                    <h4 id="stat-siswa" class="text-lg font-black text-slate-900 mt-0.5 truncate">0</h4>
                </div>
            </div>
            <span class="text-[10px] font-bold bg-slate-50 border border-slate-100 text-slate-500 px-2 py-0.5 rounded-md self-start">Limit</span>
        </div>

        <div class="bg-white border border-slate-100 p-5 rounded-2xl shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between group">
            <div class="flex items-center gap-4 min-w-0">
                <div class="p-3.5 bg-emerald-50/80 text-emerald-600 rounded-xl text-lg group-hover:scale-110 transition duration-300 shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Staf Guru</p>
                    <h4 id="stat-guru" class="text-lg font-black text-slate-900 mt-0.5 truncate">0</h4>
                </div>
            </div>
            <span class="text-[10px] font-bold bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-md self-start">Aktif</span>
        </div>

        <div class="bg-white border border-slate-100 p-5 rounded-2xl shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between group">
            <div class="flex items-center gap-4 min-w-0">
                <div class="p-3.5 bg-amber-50/80 text-amber-600 rounded-xl text-lg group-hover:scale-110 transition duration-300 shrink-0">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Tabungan</p>
                    <h4 id="stat-tabungan" class="text-lg font-black text-slate-900 mt-0.5 truncate">Rp 0</h4>
                </div>
            </div>
            <span class="text-[10px] font-bold bg-amber-50 text-amber-600 px-2 py-0.5 rounded-md self-start">Kas</span>
        </div>

        <div class="bg-white border border-slate-100 p-5 rounded-2xl shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-between group">
            <div class="flex items-center gap-4 min-w-0">
                <div class="p-3.5 bg-purple-50/80 text-purple-600 rounded-xl text-lg group-hover:scale-110 transition duration-300 shrink-0">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Log Hari Ini</p>
                    <h4 id="stat-transaksi" class="text-lg font-black text-slate-900 mt-0.5 truncate">0 Log</h4>
                </div>
            </div>
            <span class="text-[10px] font-bold bg-purple-50 text-purple-600 px-2 py-0.5 rounded-md self-start">Update</span>
        </div>
        
    </div>

    <div class="bg-white border border-slate-100 p-4 md:p-6 rounded-2xl shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 md:mb-6">
            <div>
                <h3 class="text-xs md:text-sm font-bold text-slate-900">Grafik Tabungan Pekan Ini</h3>
                <p class="text-[10px] md:text-[11px] text-slate-400 mt-0.5 hidden sm:block">Perbandingan grafik akumulasi setoran dan penarikan harian.</p>
            </div>
            <div class="flex items-center gap-3 text-[10px] md:text-[11px] font-bold">
                <div class="flex items-center gap-1.5 text-emerald-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Setoran
                </div>
                <div class="flex items-center gap-1.5 text-red-500">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Penarikan
                </div>
            </div>
        </div>
        
        <div class="w-full overflow-x-auto pb-2 scrollbar-thin">
            <div class="h-56 sm:h-64 md:h-72 min-w-[650px] sm:min-w-0 w-full relative">
                <canvas id="chartTransaksi"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Efek transisi masuk halaman saat DOM selesai di-parse
        const wrapper = document.getElementById('dashboard-wrapper');
        if(wrapper) wrapper.classList.remove('opacity-0');

        const sisaHari = parseInt(localStorage.getItem('sisa_hari_paket') || '0');
        const paketLayanan = localStorage.getItem('paket_layanan') || 'free';

        if (sisaHari <= 0) {
            const titleElem = document.getElementById('alert-title');
            const descElem = document.getElementById('alert-desc');

            if (paketLayanan === 'free') {
                if (titleElem) titleElem.innerText = "Masa Uji Coba (Trial) Habis!";
                if (descElem) descElem.innerText = "Masa uji coba 7 hari untuk Paket Free sekolah Anda telah berakhir. Fitur operasional tabungan saat ini dibekukan. Silakan hubungi Admin Utama untuk beralih ke paket Premium agar dapat melanjutkan transaksi.";
            } else {
                if (titleElem) titleElem.innerText = "Layanan Premium Kedaluwarsa!";
                if (descElem) descElem.innerText = "Masa aktif paket premium sekolah Anda telah habis (0 Hari Tersisa). Untuk menghindari gangguan layanan pada aplikasi mobile Guru dan Siswa, mohon segera lakukan perpanjangan paket.";
            }

            // Tampilkan banner merah secara instan tanpa menunggu hantaman API
            const bannerExpired = document.getElementById('banner-expired');
            if (bannerExpired) bannerExpired.classList.remove('hidden');
        }

        const IDLE_TIMEOUT = 60 * 60 * 1000; 
        let lastActivity = Date.now();

        function logActivity() { lastActivity = Date.now(); }

        function cekMasaIdle() {
            const waktuSekarang = Date.now();
            const selisihWaktu = waktuSekarang - lastActivity;
            if (selisihWaktu >= IDLE_TIMEOUT) {
                clearInterval(idleCheckInterval);
                logoutOtomatis();
            }
        }

        function logoutOtomatis() {
            fetch(API_ROUTES.logout, { method: 'POST', credentials: 'include' })
            .then(() => {
                // Bersihkan seluruh data sesi lokal sekolah
                localStorage.removeItem('nama_sekolah');
                localStorage.removeItem('paket_layanan');
                localStorage.removeItem('sisa_hari_paket');
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Sesi Berakhir!',
                    text: 'Anda telah idle selama 1 jam. Silakan masuk kembali.',
                    confirmButtonText: 'Masuk Ulang',
                    confirmButtonColor: '#2563eb',
                    allowOutsideClick: false
                }).then(() => { window.location.href = "/login"; });
            });
        }

        window.onmousedown = logActivity;
        window.onclick = logActivity;
        window.onkeydown = logActivity;
        window.onscroll = logActivity;
        window.ontouchstart = logActivity;
        var idleCheckInterval = setInterval(cekMasaIdle, 1000); 

        fetch(API_ROUTES.dashboard, {
            method: 'GET',
            credentials: 'include',
            headers: { 'Accept': 'application/json' }
        })
        .then(res => {
            if (res.status === 401 || res.status === 403) {
                window.location.href = "/login";
                return;
            }
            return res.json();
        })
        .then(resJson => {
            if (resJson && resJson.success) {
                const data = resJson.data;
                
                // Update Elemen Statistik
                document.getElementById('stat-siswa').innerText = `${data.ringkasan.total_siswa} / ${data.ringkasan.limit_siswa}`;
                document.getElementById('stat-guru').innerText = data.ringkasan.total_guru;
                document.getElementById('stat-tabungan').innerText = "Rp " + data.ringkasan.total_tabungan.toLocaleString('id-ID');
                document.getElementById('stat-transaksi').innerText = data.ringkasan.transaksi_hari_ini + " Log";

                // Perbarui data localStorage dengan data terbaru dari server jika tersedia
                if (data.sisa_hari_paket !== undefined) {
                    localStorage.setItem('sisa_hari_paket', data.sisa_hari_paket);
                }

                // Setup Canvas Context untuk Pembuatan Efek Gradasi Gradien
                const ctx = document.getElementById('chartTransaksi').getContext('2d');
                
                // Gradien Warna Hijau (Setoran)
                const gradientSetoran = ctx.createLinearGradient(0, 0, 0, 300);
                gradientSetoran.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
                gradientSetoran.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

                // Gradien Warna Merah (Penarikan)
                const gradientPenarikan = ctx.createLinearGradient(0, 0, 0, 300);
                gradientPenarikan.addColorStop(0, 'rgba(239, 68, 68, 0.20)');
                gradientPenarikan.addColorStop(1, 'rgba(239, 68, 68, 0.00)');

                // Render Chart Premium
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.grafik.labels,
                        datasets: [
                            { 
                                label: 'Setoran', 
                                data: data.grafik.setoran, 
                                borderColor: '#10b981', 
                                borderWidth: 3,
                                backgroundColor: gradientSetoran,
                                fill: true,
                                tension: 0.38,
                                pointBackgroundColor: '#10b981',
                                pointHoverRadius: 7,
                                pointRadius: 2
                            },
                            { 
                                label: 'Penarikan', 
                                data: data.grafik.penarikan, 
                                borderColor: '#ef4444', 
                                borderWidth: 3,
                                backgroundColor: gradientPenarikan,
                                fill: true,
                                tension: 0.38,
                                pointBackgroundColor: '#ef4444',
                                pointHoverRadius: 7,
                                pointRadius: 2
                            }
                        ]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                padding: 12,
                                backgroundColor: 'rgba(15, 23, 42, 0.95)', // Memperbaiki sintaks error 'sizeof' sebelumnya
                                titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                                cornerRadius: 12,
                                boxPadding: 6
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Plus Jakarta Sans', size: 10, weight: 600 }, color: '#94a3b8' }
                            },
                            y: {
                                border: { dash: [5, 5] },
                                grid: { color: '#f1f5f9' },
                                ticks: { font: { family: 'Plus Jakarta Sans', size: 10, weight: 500 }, color: '#94a3b8' }
                            }
                        }
                    }
                });
            }
        })
        .catch(err => console.error("Gagal memuat data dashboard:", err));
    });
</script>
@endsection