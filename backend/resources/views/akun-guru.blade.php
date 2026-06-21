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

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Akun Guru</h2>
        <p class="text-xs text-slate-500 mt-1">Kelola akun tenaga pengajar untuk akses sistem tabungan.</p>
    </div>
    <button id="btn-refresh-guru" onclick="loadDaftarAkunGuru()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed shrink-0">
        <i class="fa-solid fa-arrows-rotate mr-2"></i> Refresh Data
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm h-fit">
        <h3 class="text-sm font-bold text-slate-900 mb-1"><i class="fa-solid fa-plus text-blue-600 mr-1"></i> Tambah Akun Guru</h3>
        <p id="txt-tier-info" class="text-[11px] text-slate-400 mb-4">Pastikan data sesuai dengan NIP/Data sekolah.</p>
        
        <form id="form-guru" class="space-y-4">
            <input type="hidden" name="nama_lengkap" id="hidden-nama-lengkap" required>
            <input type="hidden" name="data_guru_id" id="hidden-data-guru-id" required>
            
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
        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-3">Nama Guru</th>
                        <th class="p-3">Username</th>
                        <th class="p-3">Password</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-guru-body" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="5" class="p-8 text-center text-xs text-slate-400">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data guru...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Dropdown aksi global: dipindah ke luar tabel & pakai fixed positioning
     agar tidak terpotong oleh overflow/stacking-context elemen parent manapun. -->
<div id="aksi-dropdown-global" class="hidden fixed z-50 w-44 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden text-left"></div>

<script>

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

    let guruDataMap = {}; // Menyimpan data guru per-id untuk dipakai dropdown aksi global

    // Menutup dropdown aksi global yang sedang terbuka
    function closeAllAksiDropdown() {
        document.getElementById('aksi-dropdown-global').classList.add('hidden');
    }

    // Buka/tutup dropdown aksi untuk satu baris guru tertentu.
    // Dropdown-nya satu elemen global ber-fixed-position, dipindah & diisi ulang
    // tiap kali dipanggil, supaya tidak pernah terpotong oleh overflow elemen lain.
    function toggleAksiDropdown(event, id) {
        event.stopPropagation();
        const dropdown = document.getElementById('aksi-dropdown-global');
        const btn = event.currentTarget;

        // Jika dropdown sedang terbuka untuk tombol yang sama, tutup saja (toggle)
        const isSameTarget = dropdown.dataset.openFor === String(id);
        if (!dropdown.classList.contains('hidden') && isSameTarget) {
            closeAllAksiDropdown();
            return;
        }

        const g = guruDataMap[id];
        if (!g) return;

        const isActive = !!g.is_active;
        const isUse = !!g.is_use;

        dropdown.innerHTML = `
            <button onclick="closeAllAksiDropdown(); resetPassword('${g.username}')" class="w-full px-3 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center gap-2">
                <i class="fa-solid fa-key text-amber-500 w-3.5"></i> Reset Password
            </button>
            ${isUse ? `
            <button onclick="closeAllAksiDropdown(); resetSesiGuru(${g.id}, '${g.nama_lengkap}')" class="w-full px-3 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center gap-2 border-t border-slate-100">
                <i class="fa-solid fa-rotate-left text-blue-500 w-3.5"></i> Reset Sesi
            </button>` : ''}
            <button onclick="closeAllAksiDropdown(); toggleStatusGuru(${g.id}, '${g.nama_lengkap}', ${isActive})" class="w-full px-3 py-2.5 text-xs font-semibold ${isActive ? 'text-red-600' : 'text-emerald-600'} hover:bg-slate-50 transition flex items-center gap-2 border-t border-slate-100">
                <i class="fa-solid ${isActive ? 'fa-ban' : 'fa-circle-check'} w-3.5"></i> ${isActive ? 'Nonaktifkan' : 'Aktifkan'}
            </button>
        `;

        // Hitung posisi tombol di viewport, lalu posisikan dropdown tepat di bawahnya (rata kanan)
        const rect = btn.getBoundingClientRect();
        const dropdownWidth = 176; // w-44 = 11rem = 176px
        let left = rect.right - dropdownWidth;
        if (left < 8) left = 8; // jangan sampai keluar tepi kiri viewport

        dropdown.style.left = `${left}px`;
        dropdown.style.top = `${rect.bottom + 4}px`;

        // Jika dropdown akan terpotong di bawah viewport, tampilkan ke atas tombol
        const estimatedHeight = dropdown.scrollHeight || 140;
        if (rect.bottom + 4 + estimatedHeight > window.innerHeight) {
            dropdown.style.top = `${rect.top - estimatedHeight - 4}px`;
        }

        dropdown.dataset.openFor = String(id);
        dropdown.classList.remove('hidden');
    }

    // Tutup dropdown aksi jika klik di luar area dropdown/tombol
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#aksi-dropdown-global') && !e.target.closest('[onclick^="toggleAksiDropdown"]')) {
            closeAllAksiDropdown();
        }
    });

    // Tutup dropdown aksi saat scroll atau resize, supaya tidak mengambang salah posisi
    window.addEventListener('scroll', closeAllAksiDropdown, true);
    window.addEventListener('resize', closeAllAksiDropdown);

    function loadDaftarAkunGuru() {
        const tbody = document.getElementById('table-guru-body');
        const btnRefresh = document.getElementById('btn-refresh-guru');

        // Set loading state pada tombol & tabel
        btnRefresh.disabled = true;
        btnRefresh.innerHTML = `<i class="fa-solid fa-arrows-rotate mr-2 fa-spin"></i> Memuat...`;
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="p-8 text-center text-xs text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data guru...
                </td>
            </tr>
        `;

        fetch(API_ROUTES.getAkunGuru, {
            method: 'GET',
            headers: { 
                'Accept': 'application/json' 
            },
            credentials: 'include'
        })
        .then(res => res.json())
        .then(data => {
            if(data.guru && data.guru.length > 0) {
                tbody.innerHTML = data.guru.map(g => {
                    const isActive = !!g.is_active;
                    const isUse = !!g.is_use;

                    return `
                    <tr class="hover:bg-slate-50/50 transition ${!isActive ? 'opacity-50' : ''}">
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
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${isActive ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500'}">
                                    ${isActive ? 'Aktif' : 'Nonaktif'}
                                </span>
                                ${isUse ? `<span class="inline-flex items-center text-[9px] font-bold uppercase text-blue-600" title="Sedang login"><i class="fa-solid fa-circle text-[6px] mr-1"></i>Online</span>` : ''}
                            </div>
                        </td>

                        <td class="p-3 text-center">
                            <button type="button" onclick="toggleAksiDropdown(event, ${g.id})" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 w-8 h-8 rounded-lg transition inline-flex items-center justify-center">
                                <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                            </button>
                        </td>
                    </tr>
                `}).join('');

                // Simpan data guru per-id agar bisa diakses saat membangun isi dropdown aksi global
                guruDataMap = {};
                data.guru.forEach(g => { guruDataMap[g.id] = g; });
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-xs text-slate-400">Belum ada data guru aktif.</td></tr>`;
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

    function toggleStatusGuru(id, namaGuru, isCurrentlyActive) {
        const aksiText = isCurrentlyActive ? 'menonaktifkan' : 'mengaktifkan';
        const aksiTitle = isCurrentlyActive ? 'Nonaktifkan Akun?' : 'Aktifkan Akun?';
        const warnaTombol = isCurrentlyActive ? '#dc2626' : '#16a34a';

        Swal.fire({
            icon: 'warning',
            title: aksiTitle,
            html: `Anda yakin ingin ${aksiText} akun guru <strong>${namaGuru}</strong>?${isCurrentlyActive ? '<br><span class="text-[11px] text-slate-400">Guru tidak akan bisa login ke aplikasi setelah dinonaktifkan.</span>' : ''}`,
            showCancelButton: true,
            confirmButtonText: isCurrentlyActive ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: warnaTombol,
            cancelButtonColor: '#64748b',
            width: '380px',
            customClass: {
                popup: 'rounded-2xl p-5',
                title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                htmlContainer: 'text-[12px] text-slate-500 leading-relaxed mt-1',
                confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold',
                cancelButton: 'text-sm px-4 py-2 rounded-xl font-bold'
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Memproses...',
                width: '280px',
                allowOutsideClick: false,
                customClass: { title: 'text-sm font-bold text-slate-800', popup: 'rounded-2xl p-4' },
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(API_ROUTES.toggleStatusGuru(id), {
                method: 'PATCH',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#2563eb',
                        width: '380px',
                        customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black text-slate-900 mt-2', htmlContainer: 'text-[12px] text-slate-500 mt-1', confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold' }
                    });
                    loadDaftarAkunGuru();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal memperbarui status akun.',
                        confirmButtonColor: '#ef4444',
                        width: '380px',
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
                    width: '380px',
                    customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black' }
                });
            });
        });
    }

    function resetSesiGuru(id, namaGuru) {
        Swal.fire({
            icon: 'question',
            title: 'Reset Sesi Login?',
            html: `Akun <strong>${namaGuru}</strong> akan dianggap logout, sehingga bisa login kembali dari device manapun.<br><span class="text-[11px] text-slate-400">Gunakan ini jika guru lupa logout atau perangkat hilang.</span>`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset Sesi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            width: '380px',
            customClass: {
                popup: 'rounded-2xl p-5',
                title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                htmlContainer: 'text-[12px] text-slate-500 leading-relaxed mt-1',
                confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold',
                cancelButton: 'text-sm px-4 py-2 rounded-xl font-bold'
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Memproses...',
                width: '280px',
                allowOutsideClick: false,
                customClass: { title: 'text-sm font-bold text-slate-800', popup: 'rounded-2xl p-4' },
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(API_ROUTES.resetSesiGuru(id), {
                method: 'PATCH',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#2563eb',
                        width: '380px',
                        customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black text-slate-900 mt-2', htmlContainer: 'text-[12px] text-slate-500 mt-1', confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold' }
                    });
                    loadDaftarAkunGuru();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal mereset sesi.',
                        confirmButtonColor: '#ef4444',
                        width: '380px',
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
                    width: '380px',
                    customClass: { popup: 'rounded-2xl p-5', title: 'text-sm font-black' }
                });
            });
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
                onclick="selectGuruOption(${guru.id}, '${guru.nama_guru}')">
                ${guru.nama_guru} <span class="text-[10px] text-slate-400 font-mono ml-1">(${guru.nip ? guru.nip : 'Tanpa NIP'})</span>
            </div>
        `).join('');
    }

    function selectGuruOption(id, namaGuru) {
        document.getElementById('search-guru-input').value = namaGuru;
        document.getElementById('hidden-nama-lengkap').value = namaGuru;
        document.getElementById('hidden-data-guru-id').value = id;
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
        const validDataGuruId = document.getElementById('hidden-data-guru-id').value;

        if (!validNama || !validDataGuruId) {
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
                document.getElementById('hidden-data-guru-id').value = '';
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