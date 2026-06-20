@extends('layouts.admin')
@section('title', 'Manajemen Kelas')

@section('content')
<div class="space-y-6 animate-fade-in" id="kelas-wrapper">
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
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Kelas</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola data kelas, tingkat, dan wali kelas untuk e-tabungan sekolah.</p>
        </div>
        
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div id="form-input-kelas" class="bg-white border border-slate-100 rounded-2xl shadow-xs p-6 lg:sticky lg:top-6">
            <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-50">
                <i class="fa-solid fa-square-plus text-blue-600 text-sm"></i>
                <h3 class="text-sm font-bold text-slate-900">Input Kelas Baru</h3>
            </div>

            <form id="formKelas" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nama Sekolah</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs">
                            <i class="fa-solid fa-school"></i>
                        </span>
                        <input type="text" id="input-nama-sekolah" disabled
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-500 cursor-not-allowed" 
                            value="Memuat nama sekolah...">
                    </div>
                </div>

                <div>
                    <label for="nama_kelas" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nama Kelas <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs">
                            <i class="fa-solid fa-font"></i>
                        </span>
                        <input type="text" id="nama_kelas" name="nama_kelas" required placeholder="Contoh: 1A, 6B, Melati"
                            class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 outline-none transition duration-150">
                    </div>
                </div>

                <div>
                    <label for="tingkat" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tingkat / Grade <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs">
                            <i class="fa-solid fa-arrow-up-9-1"></i>
                        </span>
                        <select id="tingkat" name="tingkat"
                            class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 rounded-xl text-xs font-medium text-slate-800 outline-none transition duration-150 appearance-none cursor-pointer">
                            <option value="">-- Pilih Tingkat (Opsional) --</option>
                            <option value="1">Tingkat 1</option>
                            <option value="2">Tingkat 2</option>
                            <option value="3">Tingkat 3</option>
                            <option value="4">Tingkat 4</option>
                            <option value="5">Tingkat 5</option>
                            <option value="6">Tingkat 6</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 text-[10px] pointer-events-none">
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>
                    </div>
                </div>

                <div>
                    <label for="guru_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Wali Kelas <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>
                        <select id="guru_id" name="guru_id"
                            class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 rounded-xl text-xs font-medium text-slate-800 outline-none transition duration-150 appearance-none cursor-pointer">
                            <option value="">-- Pilih Wali Kelas (Belum Ada) --</option>
                            </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 text-[10px] pointer-events-none">
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" id="btn-simpan-kelas" class="w-full mt-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition duration-200 flex items-center justify-center gap-2 text-xs font-bold shadow-sm shadow-blue-500/10">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Data Kelas
                </button>
            </form>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl shadow-xs p-6 lg:col-span-2">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-50">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-blue-600 text-sm"></i>
                    <h3 class="text-sm font-bold text-slate-900">Daftar Kelas Terdaftar</h3>
                </div>
                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold rounded-lg border border-slate-100" id="total-badge-kelas">0 Kelas</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/50">
                            <th class="py-3 px-4 rounded-l-xl">No</th>
                            <th class="py-3 px-4">Nama Kelas</th>
                            <th class="py-3 px-4">Tingkat</th>
                            <th class="py-3 px-4 rounded-r-xl">Wali Kelas</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-list-kelas" class="text-xs divide-y divide-slate-50 font-medium text-slate-700">
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400 font-medium">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa-solid fa-folder-open text-xl text-slate-300"></i>
                                    <span>Memuat daftar kelas sekolah...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Ambil endpoint rute master kelas yang sudah ada di api.php Anda
    // Route::get("$prefix/master/kelas", [MasterController::class, 'getAllKelas']);

    document.addEventListener("DOMContentLoaded", function() {
        
        const wrapper = document.getElementById('kelas-wrapper');
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

        const namaSekolah = localStorage.getItem('nama_sekolah') || 'Sekolah Aktif';
        const sekolahId = localStorage.getItem('sekolah_id'); // Disimpan untuk payload API jika diperlukan

        // Set nama sekolah ke input placeholder visual
        document.getElementById('input-nama-sekolah').value = namaSekolah;

        function muatDaftarKelas() {
            fetch(`${API_ROUTES.getKelas}?sekolah_id=${sekolahId}`, {
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
                    const dataKelas = resJson.data; // Sesuaikan mapping struktur data API Anda
                    const tbody = document.getElementById('tabel-list-kelas');
                    document.getElementById('total-badge-kelas').innerText = `${dataKelas.length} Kelas`;
                    
                    if(dataKelas.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="4" class="py-8 text-center text-slate-400">Belum ada kelas terdaftar.</td></tr>`;
                        return;
                    }

                    tbody.innerHTML = '';
                    dataKelas.forEach((kelas, index) => {
                        tbody.innerHTML += `
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-4 font-bold text-slate-400">${index + 1}</td>
                                <td class="py-3 px-4 font-bold text-slate-900">${kelas.nama_kelas}</td>
                                <td class="py-3 px-4"><span class="px-2 py-0.5 bg-blue-50 text-blue-600 font-bold rounded text-[10px]">${kelas.tingkat ? 'Tingkat ' + kelas.tingkat : '-'}</span></td>
                                <td class="py-3 px-4 text-slate-500">${kelas.nama_guru ? kelas.nama_guru : '<span class="text-slate-300 italic">Belum Ditentukan</span>'}</td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(err => {
                console.error("Gagal mengambil data kelas:", err);
                document.getElementById('tabel-list-kelas').innerHTML = `<tr><td colspan="4" class="py-8 text-center text-red-500">Gagal memuat data dari server.</td></tr>`;
            });
        }

        // Jalankan pemuatan otomatis saat halaman dibuka
        muatDaftarKelas();

        function muatDaftarGuru() {
            fetch(API_ROUTES.getDataGuru, {
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
                    const dataGuru = resJson.data;
                    const selectGuru = document.getElementById('guru_id');

                    selectGuru.innerHTML = '<option value="">-- Pilih Wali Kelas (Opsional) --</option>';

                    dataGuru.forEach(guru => {
                        const opt = document.createElement('option');
                        opt.value = guru.id;
                        opt.innerText = guru.nama_guru;
                        selectGuru.appendChild(opt);
                    });
                }
            })
            .catch(err => {
                console.error("Gagal mengambil data guru:", err);
            });
        }


        muatDaftarGuru();

        document.getElementById('formKelas').addEventListener('submit', function(e) {
            e.preventDefault();

            const payload = {
                sekolah_id: sekolahId, // diambil otomatis dari localStorage sekolah_id
                nama_kelas: document.getElementById('nama_kelas').value,
                tingkat: document.getElementById('tingkat').value || null,
                guru_id: document.getElementById('guru_id').value || null
            };

            // Notifikasi loading instan
            Swal.fire({
                title: 'Sedang Memproses...',
                text: 'Menyimpan data kelas baru ke server.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Ganti URL endpoint berikut sesuai rute backend penambahan kelas Anda nanti
            fetch(API_ROUTES.postKelas, { 
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(resJson => {
                if (resJson && resJson.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Kelas baru berhasil ditambahkan ke sistem.',
                        confirmButtonColor: '#2563eb'
                    });
                    
                    // Reset isi form input saja (Sekolah tetap aman)
                    document.getElementById('nama_kelas').value = '';
                    document.getElementById('tingkat').value = '';
                    document.getElementById('guru_id').value = '';

                    // Refresh isi tabel otomatis
                    muatDaftarKelas();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: resJson.message || 'Gagal menyimpan data kelas.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(err => {
                console.error("Error Saving Class:", err);
                // Simulasi Sukses Berhasil di Sisi Klien untuk keperluan visual mockup jika rute POST belum Anda buat
                Swal.fire({
                    icon: 'warning',
                    title: 'Rute API Simpan Belum Tersedia',
                    text: 'Payload berhasil disusun dengan sekolah_id: ' + sekolahId,
                    confirmButtonColor: '#2563eb'
                });
            });
        });

    });
</script>
@endsection