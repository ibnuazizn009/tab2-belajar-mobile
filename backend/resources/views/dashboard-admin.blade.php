@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<h2 class="text-xl font-black text-slate-900 tracking-tight">Ringkasan Sekolah</h2>
<p class="text-xs text-slate-500 mt-1 mb-6">Monitoring sistem keuangan terpadu E-Tabungan.</p>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center gap-4">
        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl text-lg"><i class="fa-solid fa-user-graduate"></i></div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Siswa</p>
            <h4 id="stat-siswa" class="text-lg font-black text-slate-900">0</h4>
        </div>
    </div>
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center gap-4">
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl text-lg"><i class="fa-solid fa-user-chalkboard"></i></div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Guru</p>
            <h4 id="stat-guru" class="text-lg font-black text-slate-900">0</h4>
        </div>
    </div>
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center gap-4">
        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl text-lg"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tabungan</p>
            <h4 id="stat-tabungan" class="text-lg font-black text-slate-900">Rp 0</h4>
        </div>
    </div>
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center gap-4">
        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl text-lg"><i class="fa-solid fa-arrows-rotate"></i></div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Log Hari Ini</p>
            <h4 id="stat-transaksi" class="text-lg font-black text-slate-900">0 Log</h4>
        </div>
    </div>
</div>

<div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm mt-6">
    <h3 class="text-sm font-bold text-slate-900 mb-4">Grafik Arus Kas Tabungan Minggu Ini</h3>
    <div class="h-64 relative w-full">
        <canvas id="chartTransaksi"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tokenJwt = localStorage.getItem('token_jwt');

        fetch('/api/services/tab2one/admin/dashboard', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + tokenJwt,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(resJson => {
            if (resJson.success) {
                const data = resJson.data;
                // Update Statistik
                document.getElementById('stat-siswa').innerText = `${data.ringkasan.total_siswa} / ${data.ringkasan.limit_siswa}`;
                document.getElementById('stat-guru').innerText = data.ringkasan.total_guru;
                document.getElementById('stat-tabungan').innerText = "Rp " + data.ringkasan.total_tabungan.toLocaleString('id-ID');
                document.getElementById('stat-transaksi').innerText = data.ringkasan.transaksi_hari_ini + " Log";

                // Render Chart
                const ctx = document.getElementById('chartTransaksi').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.grafik.labels,
                        datasets: [
                            { label: 'Setoran', data: data.grafik.setoran, borderColor: '#10b981', tension: 0.3, fill: false },
                            { label: 'Penarikan', data: data.grafik.penarikan, borderColor: '#ef4444', tension: 0.3, fill: false }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }
        });
    });
</script>
@endsection