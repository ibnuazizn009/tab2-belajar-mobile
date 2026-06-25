<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Sekolah Baru - E-Tabungan</title>

    <link rel="icon" type="image/x-icon" href="/favicon_ico.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }
        /* Menyelaraskan tampilan Tom Select agar senada dengan Tailwind UI */
        .ts-wrapper .ts-control {
            padding-left: 2.5rem !important;
            padding-top: 0.625rem !important;
            padding-bottom: 0.625rem !important;
            border-radius: 0.75rem !important;
            border-color: rgb(226, 232, 240) !important;
            font-size: 0.875rem !important;
        }
        .ts-wrapper.focus .ts-control {
            border-color: rgb(59, 130, 246) !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
        }
        .ts-dropdown {
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }
        /* Style border merah jika validasi simpan front-end gagal */
        .is-invalid-field {
            border-color: rgb(239, 68, 68) !important;
            background-color: rgb(254, 242, 242) !important;
        }
        .ts-wrapper.invalid .ts-control {
            border-color: rgb(239, 68, 68) !important;
            background-color: rgb(254, 242, 242) !important;
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-slate-50 via-gray-50 to-blue-50 font-sans antialiased min-h-screen flex flex-col relative overflow-x-hidden text-slate-800 pt-10">

    <!-- <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div> -->

    <div class="absolute top-6 left-6 z-50">
        <a href="{{ route('login') }}" class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-600 transition duration-200 cursor-pointer">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Login
        </a>
    </div>

    <div class="flex-grow flex items-center justify-center px-4 py-8 relative z-10">
        <div class="w-full max-w-2xl bg-white/80 backdrop-blur-md border border-slate-200/80 p-8 rounded-2xl shadow-xl shadow-slate-200/50">
            
            <div class="text-center mb-8">
                <div class="inline-flex bg-blue-600 p-3 rounded-2xl text-xl items-center justify-center shadow-lg shadow-blue-600/20 mb-4">
                    <i class="fa-solid fa-school-flag text-white text-xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun & Sekolah Baru</h2>
                <p class="text-sm text-slate-500 mt-1">Silakan lengkapi data sekolah dan data admin utama di bawah ini.</p>
            </div>

            <form action="{{ route('register.proses') }}" method="POST" class="space-y-8" id="form-registrasi" novalidate>
                @csrf 

                <div>
                    <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-2">
                        <span class="text-xs bg-blue-100 text-blue-600 font-bold px-2 py-0.5 rounded">I</span>
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Informasi Sekolah</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Pilih Paket Layanan <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-500">
                                    <i class="fa-solid fa-box-open text-sm"></i>
                                </div>
                                <select id="paket_layanan" name="paket_layanan" required onchange="updateBenefit()"
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm appearance-none">
                                    <option value="BRONZE" {{ (old('paket_layanan', $paket_terpilih ?? '') == 'BRONZE') ? 'selected' : '' }}>BRONZE TIER (Uji Coba - Gratis)</option>
                                    <option value="SILVER" {{ (old('paket_layanan', $paket_terpilih ?? '') == 'SILVER') ? 'selected' : '' }}>SILVER TIER (Rp 150k / Bulan)</option>
                                    <option value="GOLDEN" {{ (old('paket_layanan', $paket_terpilih ?? '') == 'GOLDEN') ? 'selected' : '' }}>GOLDEN TIER (Rp 350k / Bulan)</option>
                                </select>
                            </div>
                            <div id="benefit_box" class="mt-3 p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 space-y-1.5 transition-all duration-300"></div>
                            <div id="sandbox_banner" class="mt-3 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg shadow-sm hidden">
                                <h3 class="text-blue-800 font-bold text-sm">
                                    <i class="fa-solid fa-flask-vial mr-2"></i>Panduan Testing (Sandbox Mode)
                                </h3>
                                <p class="text-blue-600 text-xs mt-2 leading-relaxed">
                                    Karena aplikasi ini sedang dalam mode <b>Sandbox (Uji Coba)</b>, Anda tidak perlu membayar dengan uang asli.
                                    Setelah diarahkan ke halaman pembayaran Midtrans, gunakan simulator resmi berikut untuk menyelesaikan transaksi:
                                </p>
                                <a href="https://simulator.sandbox.midtrans.com/" target="_blank"
                                class="inline-block mt-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-xs transition">
                                    <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Buka Midtrans Simulator
                                </a>
                                <p class="text-[10px] text-blue-400 mt-3 italic">
                                    * Salin nomor Virtual Account (atau metode pembayaran lain) yang muncul di halaman Midtrans, lalu gunakan di simulator tersebut untuk menyelesaikan pembayaran uji coba.
                                </p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">NPSN Sekolah (8 Digit) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-fingerprint text-sm"></i>
                                </div>
                                <input type="text" id="npsn" name="npsn" value="{{ old('npsn') }}" required maxlength="8" placeholder="Contoh: 12345678" 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm">
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-npsn">⚠️ NPSN belum diisi</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Nama Sekolah <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-graduation-cap text-sm"></i>
                                </div>
                                <input type="text" id="nama_sekolah" name="nama_sekolah" value="{{ old('nama_sekolah') }}" required placeholder="Contoh: SMA Negeri 1 Jakarta" 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm">
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-nama_sekolah">⚠️ Nama Sekolah belum diisi</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Jenjang <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-layer-group text-sm"></i>
                                </div>
                                <select id="jenjang_id" name="jenjang_id" required 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm appearance-none">
                                    <option value="">-- Pilih Jenjang --</option>
                                    @foreach($jenjangs as $jenjang)
                                        <option value="{{ $jenjang->id }}" {{ old('jenjang_id') == $jenjang->id ? 'selected' : '' }}>
                                            {{ $jenjang->nama_jenjang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-jenjang_id">⚠️ Jenjang belum diisi</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Status Sekolah <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-building-flag text-sm"></i>
                                </div>
                                <select id="status" name="status" required 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm appearance-none">
                                    <option value="NEGERI" {{ old('status', 'NEGERI') == 'NEGERI' ? 'selected' : '' }}>NEGERI</option>
                                    <option value="SWASTA" {{ old('status') == 'SWASTA' ? 'selected' : '' }}>SWASTA</option>
                                </select>
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-status">⚠️ Status belum diisi</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Kota / Kabupaten <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 z-20">
                                    <i class="fa-solid fa-map-location-dot text-sm"></i>
                                </div>
                                <select id="searchable-kota" name="kota_id" required>
                                    <option value="">-- Pilih Kota / Kabupaten --</option>
                                    @foreach($kotas as $kota)
                                        <option value="{{ $kota->id }}" {{ old('kota_id') == $kota->id ? 'selected' : '' }}>
                                            {{ $kota->nama_kota }} ({{ $kota->provinsi }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-kota_id">⚠️ Kota / Kabupaten belum diisi</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute top-3 left-3.5 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-map-pin text-sm"></i>
                                </div>
                                <textarea id="alamat" name="alamat" rows="2" placeholder="Jl. Pendidikan No. 10..." required
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm">{{ old('alamat') }}</textarea>
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-alamat">⚠️ Alamat Lengkap belum diisi</p>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-2">
                        <span class="text-xs bg-emerald-100 text-emerald-600 font-bold px-2 py-0.5 rounded">II</span>
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Akun Admin Utama</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Nama Lengkap Petugas <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-user text-sm"></i>
                                </div>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Nama Anda" 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm">
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-nama_lengkap">⚠️ Nama Lengkap belum diisi</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Email Resmi Sekolah <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-envelope text-sm"></i>
                                </div>
                                <input type="email" id="email_sekolah" name="email_sekolah" value="{{ old('email_sekolah') }}" required placeholder="Contoh: info@sekolah.sch.id" 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm">
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-email_sekolah">⚠️ Email Sekolah belum diisi atau format salah</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">No. WhatsApp <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-brands fa-whatsapp text-base font-bold"></i>
                                </div>
                                <input type="text" id="no_whatsapp" name="no_whatsapp" value="{{ old('no_whatsapp') }}" required placeholder="Contoh: 08123456789" 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm">
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-no_whatsapp">⚠️ No. WhatsApp belum diisi</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Username Login <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-user-gear text-sm"></i>
                                </div>
                                <input type="text" id="username" name="username" value="{{ old('username') }}" required placeholder="Minimal 4 karakter" 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm">
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-username">⚠️ Username Login belum diisi</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </div>
                                <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter" 
                                    class="w-full pl-10 pr-11 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm">
                                <button type="button" id="toggle-password" tabindex="-1"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-blue-600 transition duration-150">
                                    <i class="fa-solid fa-eye text-sm" id="icon-toggle-password"></i>
                                </button>
                            </div>
                            <p class="text-[11px] text-red-500 font-medium mt-1 hidden global-error-msg" id="err-password">⚠️ Password belum diisi</p>
                        </div>
                    </div>
                </div>

                <button type="submit" id="btn-submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200 shadow-lg shadow-blue-600/20 tracking-wide flex items-center justify-center gap-2 transform active:scale-[0.98]">
                    <i id="icon-default" class="fa-solid fa-user-plus text-xs opacity-90"></i>
                    <i id="icon-loading" class="fa-solid fa-spinner fa-spin text-xs hidden"></i>
                    <span id="text-submit">Selesaikan Pendaftaran Sekolah</span>
                </button>
            </form>
        </div>
    </div>

    <footer class="w-full text-center py-6 text-xs text-slate-400 z-10 border-t border-slate-100 backdrop-blur-sm">
        &copy; {{ date('Y') }} E-Tabungan. Hak Cipta Dilindungi.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>

        const selectKota = new TomSelect("#searchable-kota",{
            create: false,
            placeholder: "-- Pilih Kota / Kabupaten --",
            allowEmptyOption: false,
            sortField: { field: "text", direction: "asc" }
        });

        // Toggle Show/Hide Password
        const togglePasswordBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const iconTogglePassword = document.getElementById('icon-toggle-password');

        togglePasswordBtn.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            iconTogglePassword.classList.toggle('fa-eye');
            iconTogglePassword.classList.toggle('fa-eye-slash');
        });

        // 2. Logika Validasi Front-End & Pengiriman Ajax (Fetch)
        const form = document.getElementById('form-registrasi');
        
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Tahan proses refresh default browser
            
            let isFormValid = true;
            
            // List element id yang wajib diperiksa kekosongannya
            const fieldsToValidate = [
                'npsn', 'nama_sekolah', 'email_sekolah', 'jenjang_id', 'status', 'alamat', 
                'nama_lengkap', 'no_whatsapp', 'username', 'password'
            ];

            // Reset seluruh tanda error lama sebelum pengecekan baru
            document.querySelectorAll('.global-error-msg').forEach(el => el.classList.add('hidden'));
            fieldsToValidate.forEach(id => document.getElementById(id).classList.remove('is-invalid-field'));
            document.querySelector('.ts-wrapper').classList.remove('invalid');
            document.getElementById('err-kota_id').classList.add('hidden');

            // Cek Isian Input & Select Standar
            fieldsToValidate.forEach(function(id) {
                const element = document.getElementById(id);
                if (!element.value.trim()) {
                    isFormValid = false;
                    element.classList.add('is-invalid-field'); // Warnai background & border merah
                    document.getElementById('err-' + id).classList.remove('hidden'); // Tampilkan pesan error
                }
            });

            // Cek Isian Spesifik Dropdown Pencarian Kota (Tom Select)
            if (!selectKota.getValue()) {
                isFormValid = false;
                document.querySelector('.ts-wrapper').classList.add('invalid');
                document.getElementById('err-kota_id').classList.remove('hidden');
            }

            // Jika terdeteksi ada kolom yang belum diisi, hentikan alur data & scroll ke atas
            if (!isFormValid) {
                const firstErrorField = document.querySelector('.is-invalid-field, .ts-wrapper.invalid');
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return; 
            }

            // --- PROSES KIRIM DATA (AJAX FETCH) ---
            const formData = new FormData(form);
            
            // Komponen Tombol untuk Loading State
            const btnSubmit = document.getElementById('btn-submit');
            const iconDefault = document.getElementById('icon-default');
            const iconLoading = document.getElementById('icon-loading');
            const textSubmit = document.getElementById('text-submit');

            // Aktifkan Mode Animasi Loading pada Tombol
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-75', 'cursor-not-allowed');
            iconDefault.classList.add('hidden');
            iconLoading.classList.remove('hidden');
            textSubmit.innerText = 'Memproses Pendaftaran...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Buat Desain Konfigurasi Toast Sukses SweetAlert2
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    // Tampilkan Toast Sukses Berhasil Terdaftar
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });

                    // Redirect otomatis ke halaman login setelah durasi toast selesai
                    setTimeout(() => {
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url; // Buka link Xendit Invoice
                        } else {
                            window.location.href = "{{ route('login') }}"; // Langsung ke login
                        }
                    }, 2000);

                } else {
                    // Matikan loading karena validasi server gagal
                    resetButtonStatus();

                    let errorText = data.message;
                    if(data.errors) {
                        errorText = Object.values(data.errors).flat().join('<br>');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Pendaftaran Gagal',
                        html: errorText,
                        confirmButtonColor: '#3b82f6'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Matikan loading karena crash jaringan / server mati
                resetButtonStatus();

                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Terjadi kegagalan koneksi atau server mengalami gangguan.',
                    confirmButtonColor: '#3b82f6'
                });
            });

            // Fungsi pembantu untuk mengembalikan tombol ke keadaan semula
            function resetButtonStatus() {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-75', 'cursor-not-allowed');
                iconDefault.classList.remove('hidden');
                iconLoading.classList.add('hidden');
                textSubmit.innerText = 'Selesaikan Pendaftaran Sekolah';
            }
        });

        // Listener dinamis untuk membersihkan border merah jika user mulai memilih kota
        selectKota.on('change', function(value) {
            if(value) {
                document.querySelector('.ts-wrapper').classList.remove('invalid');
                document.getElementById('err-kota_id').classList.add('hidden');
            }
        });

        // 3. Logika Kotak Informasi Detail Keuntungan Paket Layanan
        function updateBenefit() {
            const paket = document.getElementById('paket_layanan').value;
            const box = document.getElementById('benefit_box');
            const sandboxBanner = document.getElementById('sandbox_banner');

            let htmlContent = '';
            
            if (paket === 'BRONZE') {
                box.className = "mt-3 p-4 bg-amber-50/60 border border-amber-100 rounded-xl text-xs text-slate-700 space-y-1";
                htmlContent = `
                    <p class="font-bold text-amber-700 mb-1"><i class="fa-solid fa-circle-info mr-1"></i> Keuntungan Bronze Tier:</p>
                    <p>• Maksimal 10 Nama Siswa Aktif</p>
                    <p>• Total 2 Akun (1 Admin & 1 Akun Guru)</p>
                    <p>• Hanya 1 Kelas Kelolaan Guru</p>
                    <p>• Maksimal 5 Input Transaksi / Hari</p>
                `;
            } else if (paket === 'SILVER') {
                box.className = "mt-3 p-4 bg-blue-50/60 border border-blue-100 rounded-xl text-xs text-slate-700 space-y-1";
                htmlContent = `
                    <p class="font-bold text-blue-700 mb-1"><i class="fa-solid fa-circle-info mr-1"></i> Keuntungan Silver Tier:</p>
                    <p>• Maksimal 300 Nama Siswa Aktif</p>
                    <p>• Tambah Akun Guru <b>Tanpa Batas</b></p>
                    <p>• Guru Bisa Kelola Banyak Kelas</p>
                    <p>• Maksimal 150 Input Transaksi / Hari</p>
                    <p>• WhatsApp Notifikasi ke Orang Tua</p>
                `;
                sandboxBanner.classList.remove('hidden');
            } else if (paket === 'GOLDEN') {
                box.className = "mt-3 p-4 bg-emerald-50/60 border border-emerald-100 rounded-xl text-xs text-slate-700 space-y-1";
                htmlContent = `
                    <p class="font-bold text-emerald-700 mb-1"><i class="fa-solid fa-circle-info mr-1"></i> Keuntungan Golden Tier (Fitur Lengkap):</p>
                    <p>• Kuota Nama Siswa <b>Tanpa Batas</b></p>
                    <p>• Tambah Akun Guru <b>Tanpa Batas</b></p>
                    <p>• Input Transaksi Harian <b>Tanpa Batas</b></p>
                    <p>• WhatsApp Notifikasi ke Orang Tua</p>
                    <p>• Download Rekap Buku Tabungan (PDF/Excel)</p>
                    <p>• Import Excel Saldo Awal Siswa</p>
                `;
                sandboxBanner.classList.remove('hidden');
            }
            box.innerHTML = htmlContent;
        }

        document.addEventListener('DOMContentLoaded', updateBenefit);
    </script>
</body>
</html>