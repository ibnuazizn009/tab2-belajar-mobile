@extends('layouts.admin')
@section('title', 'Manajemen Guru')

@section('content')
<div class="space-y-6 animate-fade-in" id="data-wrapper">
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
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Guru</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola data master profil guru dan tenaga pendidik sekolah.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div id="form-input-guru" class="bg-white border border-slate-100 rounded-2xl shadow-xs p-6 lg:sticky lg:top-6">
            <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-50">
                <i class="fa-solid fa-address-card text-blue-600 text-sm"></i>
                <h3 class="text-sm font-bold text-slate-900">Input Data Guru</h3>
            </div>

            <form id="formDataGuru" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Sekolah</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs">
                            <i class="fa-solid fa-school"></i>
                        </span>
                        <input type="text" id="guru-nama-sekolah" disabled
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-500 cursor-not-allowed" value="Memuat...">
                    </div>
                </div>

                <div>
                    <label for="nip" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">NIP / No. Induk <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <input type="text" id="nip" name="nip" placeholder="Contoh: 198504..."
                            class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 rounded-xl text-xs font-medium text-slate-800 outline-none transition duration-150">
                    </div>
                </div>

                <div>
                    <label for="nama_guru" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nama Lengkap Guru <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" id="nama_guru" name="nama_guru" required placeholder="Nama Lengkap & Gelar"
                            class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 rounded-xl text-xs font-medium text-slate-800 outline-none transition duration-150">
                    </div>
                </div>

                <div>
                    <label for="jenis_kelamin" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Jenis Kelamin</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs">
                            <i class="fa-solid fa-venus-mars"></i>
                        </span>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                            class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 rounded-xl text-xs font-medium text-slate-800 outline-none transition duration-150 appearance-none cursor-pointer">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="no_hp" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">No. WhatsApp / HP</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <input type="text" id="no_hp" name="no_hp" placeholder="08xxxxx"
                            class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 rounded-xl text-xs font-medium text-slate-800 outline-none transition duration-150">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition duration-200 flex items-center justify-center gap-2 text-xs font-bold shadow-sm shadow-blue-500/10">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Data Guru
                </button>
            </form>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl shadow-xs p-6 lg:col-span-2">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-50">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-list text-blue-600 text-sm"></i>
                    <h3 class="text-sm font-bold text-slate-900">Daftar Guru Terdaftar</h3>
                </div>
                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold rounded-lg border border-slate-100" id="total-badge-guru">0 Orang</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/50">
                            <th class="py-3 px-4 rounded-l-xl">NIP</th>
                            <th class="py-3 px-4">Nama Guru</th>
                            <th class="py-3 px-4">L/P</th>
                            <th class="py-3 px-4 rounded-r-xl">No. HP</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-list-guru" class="text-xs divide-y divide-slate-50 font-medium text-slate-700">
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400">Belum ada data guru.</td>
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

    document.addEventListener("DOMContentLoaded", function() {
        
        const wrapper = document.getElementById('data-wrapper');
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

        const namaSekolah = localStorage.getItem('nama_sekolah') || 'Sekolah E-Tabungan';
        const sekolahId = localStorage.getItem('sekolah_id');
        document.getElementById('guru-nama-sekolah').value = namaSekolah;

        function muatDaftarGuru() {
            fetch(API_ROUTES.getDataGuru, { 
                method: 'GET',
                credentials: 'include',
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(resJson => {
                const tbody = document.getElementById('tabel-list-guru');
                if (resJson && resJson.success && resJson.data.length > 0) {
                    tbody.innerHTML = '';
                    document.getElementById('total-badge-guru').innerText = `${resJson.data.length} Orang`;
                    resJson.data.forEach(guru => {
                        tbody.innerHTML += `
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-4 font-bold text-slate-400">${guru.nip || '-'}</td>
                                <td class="py-3 px-4 font-bold text-slate-900">${guru.nama_guru}</td>
                                <td class="py-3 px-4">${guru.jenis_kelamin || '-'}</td>
                                <td class="py-3 px-4 text-slate-500">${guru.no_hp || '-'}</td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(() => {});
        }

        muatDaftarGuru();

        document.getElementById('formDataGuru').addEventListener('submit', function(e) {
            e.preventDefault();
            const payload = {
                sekolah_id: sekolahId,
                nip: document.getElementById('nip').value || null,
                nama_guru: document.getElementById('nama_guru').value,
                jenis_kelamin: document.getElementById('jenis_kelamin').value || null,
                no_hp: document.getElementById('no_hp').value || null
            };

            // ⏳ POPUP LOADING VERSI MINI
            Swal.fire({ 
                title: 'Menyimpan...', 
                width: '280px', // Ukuran diperkecil khusus loading
                allowOutsideClick: false, 
                customClass: {
                    title: 'text-sm font-bold text-slate-800',
                    popup: 'rounded-2xl p-4'
                },
                didOpen: () => { Swal.showLoading(); } 
            });

            fetch(API_ROUTES.postDataGuru, {
                method: 'POST',
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(resJson => {
                if (resJson && resJson.success) {
                    // 🟢 POPUP SUKSES VERSI MINI & RAMPING
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Berhasil!', 
                        text: 'Data guru berhasil ditambahkan.', 
                        confirmButtonColor: '#2563eb',
                        width: '400px', // Batasi lebar kotak agar minimalis (Default: 512px)
                        customClass: {
                            popup: 'rounded-2xl p-5',              // Kelengkungan kotak disesuaikan
                            title: 'text-sm font-black text-slate-900 tracking-tight mt-2', // Judul lebih kecil
                            htmlContainer: 'text-[12px] text-slate-500 leading-relaxed mt-1', // Deskripsi teks mini
                            confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold' // Tombol ramping
                        }
                    });
                    
                    // Reset Form
                    document.getElementById('nip').value = '';
                    document.getElementById('nama_guru').value = '';
                    document.getElementById('jenis_kelamin').value = '';
                    document.getElementById('no_hp').value = '';
                    muatDaftarGuru();
                } else {
                    // 🔴 POPUP GAGAL VERSI MINI & RAMPING
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Gagal Menyimpan', 
                        text: resJson && resJson.message ? resJson.message : 'Terjadi kesalahan sistem saat menyimpan data.', 
                        confirmButtonColor: '#ef4444',
                        width: '400px', // Batasi lebar kotak agar minimalis
                        customClass: {
                            popup: 'rounded-2xl p-5',
                            title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                            htmlContainer: 'text-[12px] text-slate-500 leading-relaxed mt-1',
                            confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold'
                        }
                    });
                }
            })
            .catch(err => {
                console.error("Error submit data guru:", err);
                // ⚠️ POPUP ERROR JARINGAN VERSI MINI & RAMPING
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Gagal',
                    text: 'Gagal terhubung ke server API. Silakan coba lagi.',
                    confirmButtonColor: '#ef4444',
                    width: '400px', // Batasi lebar kotak agar minimalis
                    customClass: {
                        popup: 'rounded-2xl p-5',
                        title: 'text-sm font-black text-slate-900 tracking-tight mt-2',
                        htmlContainer: 'text-[12px] text-slate-500 leading-relaxed mt-1',
                        confirmButton: 'text-sm px-4 py-2 rounded-xl font-bold'
                    }
                });
            });
        });
    });
</script>
@endsection