<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - E-Tabungan Sekolah</title>

    <link rel="icon" type="image/x-icon" href="/favicon_ico.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Mengunci elemen HTML dan BODY agar tidak memicu scroll horizontal karena lingkaran dekorasi */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }
       
    </style>
</head>
<body class="bg-gradient-to-tr from-slate-50 via-gray-50 to-blue-50 font-sans antialiased min-h-screen flex flex-col relative overflow-x-hidden text-slate-800 sm:pt-10">

    <!-- <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div> -->

    <div class="absolute top-6 left-6 z-50">
        <a href="{{ route('landing') }}" class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-600 transition duration-200 cursor-pointer">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Beranda
        </a>
    </div>

    <div class="flex-grow flex items-center justify-center px-4 py-8 relative z-10 w-full max-w-full">
        <div class="w-full max-w-md bg-white/80 backdrop-blur-md border border-slate-200/80 p-8 rounded-2xl shadow-xl shadow-slate-200/50">
            
            <!-- Header Form -->
            <div class="text-center mb-8">
                <div class="inline-flex bg-blue-600 p-3 rounded-2xl text-xl items-center justify-center shadow-lg shadow-blue-600/20 mb-4">
                    <i class="fa-solid fa-wallet text-white text-2xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
                <p class="text-sm text-slate-500 mt-1">Silakan masuk untuk mengelola tabungan siswa</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-sm text-red-600">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Login -->
            <form action="/api/services/tab2one/auth/login" method="POST" id="form-login" class="space-y-5">
                @csrf

                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user text-sm"></i>
                        </div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                            class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm"
                            placeholder="Masukkan username Anda">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lupa Sandi?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        
                        <input type="password" id="password" name="password" required
                            class="w-full pl-10 pr-10 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200 text-sm shadow-sm"
                            placeholder="••••••••">
                            
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                            <i class="fa-solid fa-eye text-sm" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember_me" name="remember" 
                        class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 transition duration-150 cursor-pointer">
                    <label for="remember_me" class="ml-2 text-xs font-medium text-slate-600 select-none cursor-pointer">Ingat perangkat ini</label>
                </div>

                <!-- Tombol Submit Masuk -->
                <button type="submit" id="btn-login" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200 shadow-lg shadow-blue-600/20 tracking-wide flex items-center justify-center gap-2 transform active:scale-[0.98]">
                    <span id="btn-text">Masuk ke Akun</span>
                    <i id="btn-icon" class="fa-solid fa-right-to-bracket text-xs opacity-80"></i>
                </button>
            </form>

            <!-- Navigasi Daftar Baru -->
            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Sekolah belum terdaftar? 
                    <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline ml-1">Daftar Sekarang</a>
                </p>
            </div>

        </div>
    </div>

    <footer class="w-full text-center text-xs text-slate-400 z-10 border-t border-slate-100 backdrop-blur-sm py-5">
        &copy; {{ date('Y') }} E-Tabungan. Hak Cipta Dilindungi.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const form = document.getElementById('form-login');
        const btn = document.getElementById('btn-login');
        const btnText = document.getElementById('btn-text');
        const btnIcon = document.getElementById('btn-icon');

        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');
        const usernameInput = document.getElementById('username');
        const rememberMeCheckbox = document.getElementById('remember_me');

        // 1. Logika Toggle Lihat/Sembunyikan Password
        togglePasswordBtn.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = 'fa-solid fa-eye-slash text-sm';
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = 'fa-solid fa-eye text-sm';
            }
        });

        // 2. Logika Cek Jalannya "Ingat Perangkat Ini" Saat Halaman Dimuat
        if (localStorage.getItem('remembered_username')) {
            usernameInput.value = localStorage.getItem('remembered_username');
            rememberMeCheckbox.checked = true;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Loading State
            btn.disabled = true;
            btnText.innerText = 'Memverifikasi...';
            btnIcon.className = 'fa-solid fa-spinner fa-spin';

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (rememberMeCheckbox.checked) {
                        localStorage.setItem('remembered_username', usernameInput.value);
                    } else {
                        localStorage.removeItem('remembered_username');
                    }
                    // Simpan Token
                    localStorage.setItem('nama_sekolah', data.user.nama_sekolah);
                    localStorage.setItem('paket_layanan', data.user.paket_layanan);
                    localStorage.setItem('sekolah_id', data.user.sekolah_id);
                    localStorage.setItem('sisa_hari_paket', data.user.sisa_hari_paket);
                    localStorage.setItem('foto_profil', data.user.foto_url);

                    let redirectUrl = "/dashboard-admin";
                    let alertIcon = "success";
                    let alertTitle = "Berhasil!";
                    let alertText = "Login sukses, mengalihkan...";

                    if (data.user.role === 'admin_sekolah' && data.user.sekolah) {
                        let statusBayar = data.user.sekolah.status_pembayaran;
                        if ((statusBayar === 'PENDING' || statusBayar === 'GAGAL') && data.user.retry_token) {
                            redirectUrl = "/payment/retry?token=" + data.user.retry_token;
                            alertIcon = "warning";
                            alertTitle = "Pembayaran Tertunda!";
                            alertText = "Pembayaran pendaftaran belum selesai. Mengalihkan Anda ke halaman pembayaran...";
                        }
                    }

                    
                    Swal.fire({
                        icon: 'success', 
                        title: 'Berhasil!',
                        text: 'Login sukses, mengalihkan...',
                        timer: 1500,
                        showConfirmButton: false,
                        width: 'auto',              
                        padding: '1.5em',           
                        customClass: {
                            popup: 'max-w-[400px] w-[90%] text-sm' 
                        }
                    });
                    
                    setTimeout(() => { window.location.href = redirectUrl; }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error', 
                        title: 'Login Gagal!',
                        text: data.message || 'Username atau password salah',
                        showConfirmButton: true,
                        width: 'auto',             
                        padding: '1em',    
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#ef4444',
                        customClass: {
                            popup: 'max-w-[400px] w-[90%] text-sm' 
                        }
                    });
                    resetBtn();
                }
            })
            .catch(() => {
                 Swal.fire({
                    icon: 'error', 
                    title: 'Login Gagal!',
                    text: 'Gagal menghubungi server',
                    width: '85%',         
                    showConfirmButton: true,
                    width: 'auto',              
                    padding: '1em',    
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'max-w-[400px] w-[90%] text-sm' 
                    }
                });
                resetBtn();
            });
        });

        function resetBtn() {
            btn.disabled = false;
            btnText.innerText = 'Masuk ke Akun';
            btnIcon.className = 'fa-solid fa-right-to-bracket text-xs opacity-80';
        }
    </script>
</body>
</html>