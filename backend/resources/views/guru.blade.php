@extends('layouts.admin')
@section('title', 'Akun Guru')

@section('content')
<h2 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Guru</h2>
<p class="text-xs text-slate-500 mt-1 mb-6">Kelola data tenaga pengajar untuk akses sistem tabungan.</p>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Kolom Kiri: Form Tambah Guru -->
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm h-fit">
        <h3 class="text-sm font-bold text-slate-900 mb-1"><i class="fa-solid fa-plus text-blue-600 mr-1"></i> Tambah Guru Baru</h3>
        <p id="txt-tier-info" class="text-[11px] text-slate-400 mb-4">Pastikan data sesuai dengan NIP/Data sekolah.</p>
        
        <form id="form-guru" class="space-y-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                Daftarkan Akun
            </button>
        </form>
    </div>

    <!-- Kolom Kanan: Tabel Guru -->
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm lg:col-span-2">
        <h3 class="text-sm font-bold text-slate-900 mb-4"><i class="fa-solid fa-users text-blue-600 mr-1"></i> Daftar Guru Aktif</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-3">Nama Guru</th>
                        <th class="p-3">Username</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-guru-body" class="divide-y divide-slate-100">
                    <tr><td colspan="3" class="p-4 text-center text-xs text-slate-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const tokenJwt = localStorage.getItem('token_jwt');

    // Load Daftar Guru
    function loadDaftarGuru() {
        fetch('/api/services/tab2one/admin/guru', {
            headers: { 'Authorization': 'Bearer ' + tokenJwt }
        })
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('table-guru-body');
            if(data.guru.length > 0) {
                tbody.innerHTML = data.guru.map(g => `
                    <tr>
                        <td class="p-3 font-semibold text-slate-800">${g.nama_lengkap}</td>
                        <td class="p-3 font-mono text-xs">${g.username}</td>
                        <td class="p-3 text-center">
                            <button onclick="resetPassword('${g.username}')" class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-600 px-3 py-1 rounded-lg border border-amber-200">Reset</button>
                        </td>
                    </tr>
                `).join('');
            }
        });
    }

    // Submit Form
    document.getElementById('form-guru').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('/api/services/tab2one/admin/guru', {
            method: 'POST',
            body: new FormData(this),
            headers: { 'Authorization': 'Bearer ' + tokenJwt }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire('Sukses', 'Guru berhasil ditambahkan', 'success');
                this.reset();
                loadDaftarGuru();
            }
        });
    });

    document.addEventListener("DOMContentLoaded", loadDaftarGuru);
</script>
@endsection