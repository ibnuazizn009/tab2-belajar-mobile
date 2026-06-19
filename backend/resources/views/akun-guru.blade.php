@extends('layouts.admin')
@section('title', 'Akun Guru')

@section('content')
<div id="banner-expired" class="hidden mb-4 bg-red-50 border border-red-200 rounded-xl p-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 animate-pulse mb-[30px]">
    <div class="flex items-center gap-2.5 min-w-0 flex-1">
        <div class="p-1.5 bg-red-100 text-red-600 rounded-lg text-sm shrink-0">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="min-w-0 flex-1">
            <h5 id="alert-title" class="text-sm font-extrabold text-red-900 leading-tight">Masa Aktif Paket Habis!</h5>
            <p id="alert-desc" class="text-[12px] text-red-600 mt-0.5 leading-normal">Masa aktif layanan sekolah Anda telah berakhir (0 Hari Tersisa). Silakan lakukan pembaruan paket segera.</p>
        </div>
    </div>
    <div class="w-full sm:w-auto shrink-0 pl-9 sm:pl-0">
        <a href="/admin/billing" id="btn-update-paket" class="inline-flex items-center justify-center gap-1.5 w-full sm:w-auto px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold rounded-lg shadow-sm transition-all hover:scale-[1.01] active:scale-[0.99]">
            <i class="fa-solid fa-credit-card text-[9px]"></i>
            <span>Perbarui Paket</span>
        </a>
    </div>
</div>

<h2 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Akun Guru</h2>
<p class="text-xs text-slate-500 mt-1 mb-6">Kelola akun tenaga pengajar untuk akses sistem tabungan.</p>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm h-fit">
        <h3 class="text-sm font-bold text-slate-900 mb-1"><i class="fa-solid fa-plus text-blue-600 mr-1"></i> Tambah Akun Guru</h3>
        <p id="txt-tier-info" class="text-[11px] text-slate-400 mb-4">Pastikan data sesuai dengan NIP/Data sekolah.</p>
        
        <form id="form-guru" class="space-y-4">
            <input type="hidden" name="nama_lengkap" id="hidden-nama-lengkap" required>
            
            <div class="relative" id="combobox-container">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap Guru</label>
                <div class="relative">
                    <input type="text" id="search-guru-input" autocomplete="off" placeholder="Ketik untuk mencari nama guru..." class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 pr-10">
                    <div class="absolute right-3 top-2.5 text-slate-400 pointer-events-none text-xs" id="combobox-icon">
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>
                
                <div id="guru-dropdown-list" class="hidden absolute z-20 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto divide-y divide-slate-50">
                    <div class="p-3 text-center text-xs text-slate-400 py-4">Memuat data guru...</div>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password-input" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 pr-10">
                    <button type="button" id="btn-toggle-password" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 focus:outline-none text-xs transition">
                        <i class="fa-solid fa-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                Daftarkan Akun
            </button>
        </form>
    </div>

    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm lg:col-span-2">
        <h3 class="text-sm font-bold text-slate-900 mb-4"><i class="fa-solid fa-users text-blue-600 mr-1"></i> Daftar Guru Aktif</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-3">Nama Guru</th>
                        <th class="p-3">Username</th>
                        <th class="p-3">Password</th> 
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-guru-body" class="divide-y divide-slate-100">
                    <tr><td colspan="4" class="p-4 text-center text-xs text-slate-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const tokenJwt = localStorage.getItem('token_jwt');
    let masterGuruData = []; // Menyimpan salinan data master guru dari API

    function toggleRowPassword(btn) {
        const container = btn.parentElement;
        const maskedSpan = container.querySelector('.pwd-masked');
        const rawSpan = container.querySelector('.pwd-raw');
        const icon = btn.querySelector('i');

        if (rawSpan.classList.contains('hidden')) {
            rawSpan.classList.remove('hidden');
            maskedSpan.classList.add('hidden');
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            rawSpan.classList.add('hidden');
            maskedSpan.classList.remove('hidden');
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    document.getElementById('btn-toggle-password').addEventListener('click', function() {
        const passwordInput = document.getElementById('password-input');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            // Ubah ikon jadi mata dicoret (hide)
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            // Kembalikan ikon jadi mata normal (show)
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });

    function loadDaftarAkunGuru() {
        fetch(API_ROUTES.getAkunGuru, {
            method: 'GET',
            headers: { 
                'Accept': 'application/json' 
            },
            credentials: 'include'
        })
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('table-guru-body');
            if(data.guru && data.guru.length > 0) {
                tbody.innerHTML = data.guru.map(g => `
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-3 font-semibold text-slate-800">${g.nama_lengkap}</td>
                        <td class="p-3 font-mono text-xs text-slate-500">${g.username}</td>
                        
                        <td class="p-3 font-mono text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <span class="pwd-masked tracking-widest text-slate-400">••••••••</span>
                                <span class="pwd-raw hidden select-all">${g.password || '******'}</span>
                                <button type="button" onclick="toggleRowPassword(this)" class="text-slate-400 hover:text-slate-600 focus:outline-none transition">
                                    <i class="fa-solid fa-eye text-[10px]"></i>
                                </button>
                            </div>
                        </td>

                        <td class="p-3 text-center">
                            <button onclick="resetPassword('${g.username}')" class="text-[11px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-600 px-2.5 py-1 rounded-lg border border-amber-200 transition">Reset</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-xs text-slate-400">Belum ada data guru aktif.</td></tr>`;
            }
        });
    }

    function resetPassword(username) {
        Swal.fire({
            title: 'Reset Password',
            html: `Masukkan password baru untuk akun <strong>${username}</strong>:`,
            input: 'text', // Menggunakan tipe text agar admin bisa melihat apa yang diketik
            inputPlaceholder: 'Ketik password baru di sini...',
            inputAttributes: {
                autocapitalize: 'off',
                autocomplete: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Simpan Password',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563eb', // Warna biru primer Anda
            cancelButtonColor: '#64748b',  // Warna slate gray
            
            // Validasi input di dalam pop-up
            inputValidator: (value) => {
                if (!value) {
                    return 'Password baru tidak boleh kosong!';
                }
                if (value.length < 4) {
                    return 'Password minimal harus 4 karakter!';
                }
            },
            width: '360px',
            customClass: {
                popup: 'rounded-2xl p-5',
                title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                htmlContainer: 'text-[12px] text-slate-500 mt-1',
                input: 'text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-0 mx-auto w-full c-input-swal',
                confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold',
                cancelButton: 'text-sm px-4 py-2 rounded-xl font-bold'
            }
        }).then((result) => {
            // Jika Admin menekan tombol "Simpan Password"
            if (result.isConfirmed) {
                const passwordBaru = result.value;

                // Tampilkan loading spinner saat request dikirim
                Swal.fire({ 
                    title: 'Memproses Reset...', 
                    width: '280px',
                    allowOutsideClick: false, 
                    customClass: {
                        title: 'text-sm font-bold text-slate-800',
                        popup: 'rounded-2xl p-4'
                    },
                    didOpen: () => { Swal.showLoading(); } 
                });

                // Kirim data ke Backend API (Pastikan API_ROUTES.resetPasswordGuru sudah didefinisikan)
                fetch(API_ROUTES.resetPasswordGuru, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username,
                        password: passwordBaru
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'Berhasil!', 
                            html: `Password untuk akun <strong>${username}</strong> berhasil diperbarui.`,
                            confirmButtonColor: '#2563eb',
                            width: '400px',
                            customClass: {
                                popup: 'rounded-2xl p-5',
                                title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                                htmlContainer: 'text-[12px] text-slate-500 leading-relaxed mt-1',
                                confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold'
                            }
                        });
                        
                        // Refresh data tabel agar password barunya langsung termuat
                        loadDaftarAkunGuru();
                    } else {
                        Swal.fire({ 
                            icon: 'error', 
                            title: 'Gagal', 
                            text: data.message || 'Gagal mereset password.', 
                            confirmButtonColor: '#ef4444',
                            width: '400px',
                            customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black' }
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal',
                        text: 'Gagal terhubung ke server.',
                        confirmButtonColor: '#ef4444',
                        width: '400px',
                        customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black' }
                    });
                });
            }
        });
    }

    function fetchMasterGuruForCombobox() {
        // Ganti API_ROUTES.dataGuruList jika rute list data guru Anda berbeda
        fetch(API_ROUTES.getDataGuru, {
            method: 'GET',
            headers: { 
                'Accept': 'application/json' 
            },
            credentials: 'include'
        })
        .then(res => res.json())
        .then(resJson => {
            if (resJson.success && resJson.data) {
                masterGuruData = resJson.data; // Simpan data ke dalam array global
                renderComboboxItems(masterGuruData);
            } else {
                document.getElementById('guru-dropdown-list').innerHTML = `<div class="p-3 text-center text-xs text-red-500 py-3">Gagal memuat data master guru.</div>`;
            }
        })
        .catch(() => {
            document.getElementById('guru-dropdown-list').innerHTML = `<div class="p-3 text-center text-xs text-red-500 py-3">Gagal menghubungkan ke server.</div>`;
        });
    }

    function renderComboboxItems(items) {
        const dropdown = document.getElementById('guru-dropdown-list');
        if (items.length === 0) {
            dropdown.innerHTML = `<div class="p-3 text-center text-xs text-slate-400 py-3">Tidak ada data guru cocok.</div>`;
            return;
        }

        dropdown.innerHTML = items.map(guru => `
            <div class="p-2.5 px-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer font-medium transition" 
                 onclick="selectGuruOption('${guru.nama_guru}')">
                ${guru.nama_guru} <span class="text-[10px] text-slate-400 font-mono ml-1">(${guru.nip ? guru.nip : 'Tanpa NIP'})</span>
            </div>
        `).join('');
    }

    function selectGuruOption(namaGuru) {
        document.getElementById('search-guru-input').value = namaGuru;
        document.getElementById('hidden-nama-lengkap').value = namaGuru;
        document.getElementById('guru-dropdown-list').classList.add('hidden');
    }

    // Event Listener Kendali Combobox Input
    const searchInput = document.getElementById('search-guru-input');
    const dropdownList = document.getElementById('guru-dropdown-list');

    searchInput.addEventListener('focus', () => {
        dropdownList.classList.remove('hidden');
    });

    searchInput.addEventListener('input', (e) => {
        const keyword = e.target.value.toLowerCase();
        // Filter array data guru berdasarkan text input keyword
        const filtered = masterGuruData.filter(g => g.nama_guru.toLowerCase().includes(keyword));
        renderComboboxItems(filtered);
        
        // Bersihkan data hidden input jika user mengubah isian manual tanpa klik opsi resmi
        document.getElementById('hidden-nama-lengkap').value = '';
    });

    // Menutup dropdown jika user klik di luar area combobox
    document.addEventListener('click', (e) => {
        const container = document.getElementById('combobox-container');
        if (!container.contains(e.target)) {
            dropdownList.classList.add('hidden');
        }
    });

    document.getElementById('form-guru').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validasi: Pastikan user memilih guru dari opsi resmi combobox
        const validNama = document.getElementById('hidden-nama-lengkap').value;
        if (!validNama) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilihan Tidak Valid',
                text: 'Harap pilih nama guru yang tersedia dari daftar pilihan!',
                confirmButtonColor: '#2563eb',
                width: '340px',
                customClass: {
                    popup: 'rounded-2xl p-5',
                    title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                    htmlContainer: 'text-[11px] text-slate-500 leading-relaxed mt-1',
                    confirmButton: 'text-xs px-4 py-2 rounded-xl font-bold'
                }
            });
            return;
        }

        // ⏳ POPUP LOADING MINI
        Swal.fire({ 
            title: 'Mendaftarkan Akun...', 
            width: '280px',
            allowOutsideClick: false, 
            customClass: {
                title: 'text-sm font-bold text-slate-800',
                popup: 'rounded-2xl p-4'
            },
            didOpen: () => { Swal.showLoading(); } 
        });

        fetch(API_ROUTES.postAkunGuru, {
            method: 'POST',
            credentials: 'include',
            body: new FormData(this),
            headers: {'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // 🟢 POPUP SUKSES MINI
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil!', 
                    text: 'Akun guru berhasil didaftarkan.', 
                    confirmButtonColor: '#2563eb',
                    width: '400px',
                    customClass: {
                        popup: 'rounded-2xl p-5',
                        title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                        htmlContainer: 'text-[12px] text-slate-500 leading-relaxed mt-1',
                        confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold'
                    }
                });
                this.reset();
                searchInput.value = ''; // Reset display search text manual
                loadDaftarAkunGuru();
            } else {
                // 🔴 POPUP GAGAL MINI
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Gagal Mendaftar', 
                    text: data.message ? data.message : 'Terjadi kesalahan sistem.', 
                    confirmButtonColor: '#ef4444',
                    width: '400px',
                    customClass: {
                        popup: 'rounded-2xl p-5',
                        title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                        htmlContainer: 'text-[12px] text-slate-500 leading-relaxed mt-1',
                        confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold'
                    }
                });

                this.reset();
                searchInput.value = '';
            }
        })
        .catch(err => {
            console.error("Error submit akun guru:", err);
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Gagal terhubung ke server API.',
                confirmButtonColor: '#ef4444',
                width: '400px',
                customClass: {
                    popup: 'rounded-2xl p-5',
                    title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                    htmlContainer: 'text-[12px] text-slate-500 leading-relaxed mt-1',
                    confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold'
                }
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
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

            const bannerExpired = document.getElementById('banner-expired');
            if (bannerExpired) bannerExpired.classList.remove('hidden');
        }

        loadDaftarAkunGuru();
        fetchMasterGuruForCombobox(); // Ambil list data master guru untuk combobox pilihan
    });
</script>
@endsection