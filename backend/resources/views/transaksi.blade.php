@extends('layouts.admin')
@section('title', 'Transaksi')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900">Log Transaksi Global</h2>
        <p class="text-xs text-slate-500">Mutasi tabungan real-time dari seluruh siswa.</p>
    </div>
    <button id="btn-refresh-transaksi" onclick="loadDaftarTransaksi()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed">
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
    function formatWaktu(isoString) {
        const d = new Date(isoString);
        const tanggal = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        const jam = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        return `${tanggal}, ${jam}`;
    }

    function loadDaftarTransaksi() {
        const tbody = document.getElementById('table-transaksi-body');
        const btnRefresh = document.getElementById('btn-refresh-transaksi');

        // Set loading state pada tombol & tabel
        btnRefresh.disabled = true;
        btnRefresh.innerHTML = `<i class="fa-solid fa-arrows-rotate mr-2 fa-spin"></i> Memuat...`;
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="p-8 text-center text-xs text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data transaksi...
                </td>
            </tr>
        `;

        fetch(API_ROUTES.getTransaksi, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(t => {
                    const isSetor = t.tipe === 'setor';
                    const namaKelas = t.siswa?.kelas?.nama_kelas ?? '-';

                    return `
                        <tr>
                            <td class="p-4 text-xs text-slate-500">${formatWaktu(t.created_at)}</td>
                            <td class="p-4">
                                <p class="font-semibold text-slate-800">${t.siswa?.nama_siswa ?? '-'}</p>
                                <p class="text-[11px] text-slate-400">Kelas ${namaKelas}</p>
                            </td>
                            <td class="p-4 text-xs">${t.petugas?.nama_guru ?? '-'}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${isSetor ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}">
                                    ${t.tipe}
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold ${isSetor ? 'text-emerald-600' : 'text-red-600'}">
                                ${isSetor ? '+' : '-'} Rp ${t.nominal.toLocaleString('id-ID')}
                            </td>
                        </tr>
                    `;
                }).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="p-8 text-center text-xs text-slate-400">Belum ada transaksi.</td></tr>`;
            }
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="5" class="p-8 text-center text-xs text-red-400">Gagal memuat data.</td></tr>`;
        })
        .finally(() => {
            btnRefresh.disabled = false;
            btnRefresh.innerHTML = `<i class="fa-solid fa-arrows-rotate mr-2"></i> Refresh Data`;
        });
    }

    document.addEventListener("DOMContentLoaded", loadDaftarTransaksi);
</script>
@endsection