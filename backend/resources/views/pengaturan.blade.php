@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<h2 class="text-xl font-black text-slate-900 tracking-tight">Pengaturan Akun</h2>
<p class="text-xs text-slate-500 mt-1 mb-6">Kelola profil dan keamanan akun Anda.</p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-1"><i class="fa-solid fa-camera text-blue-600 mr-1"></i> Foto Profil</h3>
        <p class="text-[11px] text-slate-400 mb-6">Perbarui foto profil Anda untuk identitas akun. Maks. 2MB (JPG/PNG/WEBP).</p>
        
        <div class="flex items-center gap-6">
            <img id="profile-preview" src="https://ui-avatars.com/api/?background=3b82f6&color=fff&name=Admin" class="w-24 h-24 rounded-2xl object-cover border border-slate-200">
            <form id="form-foto" class="space-y-3 w-full" enctype="multipart/form-data">
                <input type="file" name="foto" id="input-foto" accept="image/png, image/jpeg, image/webp" onchange="previewImage(event)" class="w-full text-[11px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <button type="submit" id="btn-simpan-foto" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Simpan Foto
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-1"><i class="fa-solid fa-shield-halved text-blue-600 mr-1"></i> Ganti Password</h3>
        <p class="text-[11px] text-slate-400 mb-4">Ubah password untuk keamanan akun Anda. Minimal 6 karakter.</p>
        
        <form id="form-password" class="space-y-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Password Lama</label>
                <input type="text" name="current_password" autocomplete="off" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Password Baru</label>
                <div class="relative">
                    <input type="password" name="new_password" id="input-new-password" autocomplete="off" required minlength="6" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 pr-10">
                    <button type="button" id="btn-toggle-new-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none text-xs transition">
                        <i class="fa-solid fa-eye" id="icon-toggle-new-password"></i>
                    </button>
                </div>
            </div>
            <button type="submit" id="btn-simpan-password" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                Perbarui Password
            </button>
        </form>
    </div>
</div>

<script>
    // CATATAN: tidak pakai localStorage.getItem('token_jwt') atau header
    // Authorization manual — auth web admin di Tab2One pakai JWT HttpOnly
    // cookie, jadi cukup credentials: 'include' seperti endpoint lain
    // (akun-guru, kelas, data-guru). Cookie dikirim otomatis oleh browser.

    // Tampilkan foto profil yang sudah tersimpan, kalau ada,
    // supaya tetap muncul setelah halaman di-reload.
    document.addEventListener('DOMContentLoaded', function() {
        const fotoTersimpan = localStorage.getItem('foto_profil');
        if (fotoTersimpan) {
            const profilePreview = document.getElementById('profile-preview');
            if (profilePreview) profilePreview.src = fotoTersimpan;
        }
    });

    // Tampilkan preview foto sebelum benar-benar di-upload ke server
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() { document.getElementById('profile-preview').src = reader.result; }
        if (event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
    }

    // Toggle tampil/sembunyi untuk password baru
    document.getElementById('btn-toggle-new-password').addEventListener('click', function() {
        const inputNewPassword = document.getElementById('input-new-password');
        const icon = document.getElementById('icon-toggle-new-password');

        if (inputNewPassword.type === 'password') {
            inputNewPassword.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            inputNewPassword.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Submit Foto Profil
    document.getElementById('form-foto').addEventListener('submit', function(e) {
        e.preventDefault();

        const inputFoto = document.getElementById('input-foto');
        if (!inputFoto.files[0]) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Ada Foto',
                text: 'Pilih file foto terlebih dahulu sebelum menyimpan.',
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        const btnSimpan = document.getElementById('btn-simpan-foto');
        const teksAsli = btnSimpan.innerHTML;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> Mengunggah...`;

        fetch(API_ROUTES.uploadFoto, {
            method: 'POST',
            body: new FormData(this),
            credentials: 'include',
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Foto profil diperbarui.',
                    confirmButtonColor: '#2563eb',
                    width: '380px',
                    customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black text-slate-900 mt-2', htmlContainer: 'text-[12px] text-slate-500 mt-1', confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold' }
                });

                // Sinkronkan foto di sidebar/navbar layout admin + persisten lewat localStorage
                if (data.foto_url) {
                    localStorage.setItem('foto_profil', data.foto_url);

                    const profilePreview = document.getElementById('profile-preview');
                    const sidebarAvatar = document.getElementById('sidebar-avatar');
                    const navbarAvatar = document.getElementById('top-navbar-avatar');

                    if (profilePreview) profilePreview.src = data.foto_url;
                    if (sidebarAvatar) sidebarAvatar.src = data.foto_url;
                    if (navbarAvatar) navbarAvatar.src = data.foto_url;
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Gagal memperbarui foto profil.',
                    confirmButtonColor: '#ef4444',
                    width: '380px',
                    customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black' }
                });
            }
        })
        .catch(err => {
            console.error('Error upload foto:', err);
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Gagal terhubung ke server.',
                confirmButtonColor: '#ef4444',
                width: '380px',
                customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black' }
            });
        })
        .finally(() => {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = teksAsli;
        });
    });

    // Submit Ganti Password
    document.getElementById('form-password').addEventListener('submit', function(e) {
        e.preventDefault();

        const newPassword = document.getElementById('input-new-password').value;
        if (newPassword.length < 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Password Terlalu Pendek',
                text: 'Password baru minimal harus 6 karakter.',
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        const btnSimpan = document.getElementById('btn-simpan-password');
        const teksAsli = btnSimpan.innerHTML;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...`;

        const formEl = this;

        fetch(API_ROUTES.resetPasswordAdmin, {
            method: 'POST',
            body: new FormData(this),
            credentials: 'include',
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: (data.message || 'Password berhasil diperbarui.') + '<br><span class="text-[11px] text-slate-400">Anda akan diarahkan ke halaman login.</span>',
                    confirmButtonColor: '#2563eb',
                    width: '380px',
                    allowOutsideClick: false,
                    customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black text-slate-900 mt-2', htmlContainer: 'text-[12px] text-slate-500 mt-1', confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold' }
                }).then(() => {
                    if (data.force_logout) {
                        localStorage.clear();
                        window.location.href = '/login';
                    } else {
                        formEl.reset();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Gagal memperbarui password.',
                    confirmButtonColor: '#ef4444',
                    width: '380px',
                    customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black' }
                });
            }
        })
        .catch(err => {
            console.error('Error ganti password:', err);
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Gagal terhubung ke server.',
                confirmButtonColor: '#ef4444',
                width: '380px',
                customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black' }
            });
        })
        .finally(() => {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = teksAsli;
        });
    });
</script>
@endsection