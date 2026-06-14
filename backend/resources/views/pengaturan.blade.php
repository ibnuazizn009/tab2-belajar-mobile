@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<h2 class="text-xl font-black text-slate-900 tracking-tight">Pengaturan Akun</h2>
<p class="text-xs text-slate-500 mt-1 mb-6">Kelola profil dan keamanan akun Anda.</p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-1"><i class="fa-solid fa-camera text-blue-600 mr-1"></i> Foto Profil</h3>
        <p class="text-[11px] text-slate-400 mb-6">Perbarui foto profil Anda untuk identitas akun.</p>
        
        <div class="flex items-center gap-6">
            <img id="profile-preview" src="https://ui-avatars.com/api/?background=3b82f6&color=fff&name=Admin" class="w-24 h-24 rounded-2xl object-cover border border-slate-200">
            <form id="form-foto" class="space-y-3 w-full" enctype="multipart/form-data">
                <input type="file" name="foto" id="input-foto" onchange="previewImage(event)" class="w-full text-[11px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-sm transition">
                    Simpan Foto
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-1"><i class="fa-solid fa-shield-halved text-blue-600 mr-1"></i> Ganti Password</h3>
        <p class="text-[11px] text-slate-400 mb-4">Ubah password untuk keamanan akun Anda.</p>
        
        <form id="form-password" class="space-y-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Password Lama</label>
                <input type="password" name="current_password" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Password Baru</label>
                <input type="password" name="new_password" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                Perbarui Password
            </button>
        </form>
    </div>
</div>

<script>
    const tokenJwt = localStorage.getItem('token_jwt');

    // Preview Foto sebelum upload
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() { document.getElementById('profile-preview').src = reader.result; }
        if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
    }

    // Submit Foto Profil
    document.getElementById('form-foto').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('/api/services/tab2one/admin/update-foto', {
            method: 'POST',
            body: new FormData(this),
            headers: { 'Authorization': 'Bearer ' + tokenJwt }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) Swal.fire('Berhasil', 'Foto diperbarui', 'success');
            else Swal.fire('Gagal', data.message, 'error');
        });
    });

    // Submit Ganti Password
    document.getElementById('form-password').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('/api/services/tab2one/admin/update-password', {
            method: 'POST',
            body: new FormData(this),
            headers: { 'Authorization': 'Bearer ' + tokenJwt }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire('Berhasil', 'Password diperbarui', 'success');
                this.reset();
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        });
    });
</script>
@endsection