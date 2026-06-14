@extends('layouts.admin')
@section('title', 'Transaksi')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900">Log Transaksi Global</h2>
        <p class="text-xs text-slate-500">Mutasi tabungan real-time dari seluruh siswa.</p>
    </div>
    <button onclick="window.location.reload()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
        <i class="fa-solid fa-arrows-rotate mr-2"></i> Refresh Data
    </button>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                <tr>
                    <th class="p-4">Waktu</th>
                    <th class="p-4">Siswa</th>
                    <th class="p-4">Petugas</th>
                    <th class="p-4">Jenis</th>
                    <th class="p-4 text-right">Nominal</th>
                </tr>
            </thead>
            <tbody id="table-transaksi-body" class="divide-y divide-slate-100">
                <tr>
                    <td colspan="5" class="p-8 text-center text-xs text-slate-400">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data transaksi...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    const tokenJwt = localStorage.getItem('token_jwt');

    function loadDaftarTransaksi() {
        fetch('/api/services/tab2one/admin/transaksi', {
            method: 'GET',
            headers: { 
                'Authorization': 'Bearer ' + tokenJwt,
                'Accept': 'application/json' 
            }
        })
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('table-transaksi-body');
            
            if (data.success && data.transaksi.length > 0) {
                tbody.innerHTML = data.transaksi.map(t => `
                    <tr>
                        <td class="p-4 text-xs text-slate-500">${t.waktu}</td>
                        <td class="p-4 font-semibold text-slate-800">${t.nama_siswa}</td>
                        <td class="p-4 text-xs">${t.nama_petugas}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${t.jenis === 'SETOR' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}">
                                ${t.jenis}
                            </span>
                        </td>
                        <td class="p-4 text-right font-bold ${t.jenis === 'SETOR' ? 'text-emerald-600' : 'text-red-600'}">
                            ${t.jenis === 'SETOR' ? '+' : '-'} Rp ${t.nominal.toLocaleString('id-ID')}
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="p-8 text-center text-xs text-slate-400">Belum ada transaksi.</td></tr>`;
            }
        })
        .catch(err => {
            document.getElementById('table-transaksi-body').innerHTML = `<tr><td colspan="5" class="p-8 text-center text-xs text-red-400">Gagal memuat data.</td></tr>`;
        });
    }

    document.addEventListener("DOMContentLoaded", loadDaftarTransaksi);
</script>
@endsection