<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Aplikasi - E-Tabungan Sekolah</title>

    <link rel="icon" type="image/x-icon" href="/favicon_ico.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html, body { overflow-x: hidden; width: 100%; }

        /* ================================================================= */
        /* SYSTEM ANIMASI LIVE MOCKUP HP (CSS KEYFRAMES)                     */
        /* ================================================================= */
        
        /* 1. Layar Login (Muncul di awal, menghilang di detik ke-3.5) */
        .animate-login-screen {
            animation: loginScreenAnim 8s infinite ease-in-out;
        }
        @keyframes loginScreenAnim {
            0%, 38% { opacity: 1; visibility: visible; }
            42%, 95% { opacity: 0; visibility: hidden; }
            100% { opacity: 1; visibility: visible; }
        }

        /* 2. Simulasi Ketik Username */
        .animate-type-user {
            display: inline-block;
            overflow: hidden;
            white-space: nowrap;
            width: 0;
            animation: typeUsername 8s infinite linear;
        }
        @keyframes typeUsername {
            0%, 5% { width: 0; }
            18%, 100% { width: 75px; }
        }

        /* 3. Simulasi Ketik Password */
        .animate-type-pass {
            display: inline-block;
            overflow: hidden;
            white-space: nowrap;
            width: 0;
            animation: typePassword 8s infinite linear;
        }
        @keyframes typePassword {
            0%, 20% { width: 0; }
            32%, 100% { width: 60px; }
        }

        /* 4. Efek Tombol Login Diklik (Slight Scale Down) */
        .animate-click-btn {
            animation: clickButton 8s infinite ease-in-out;
        }
        @keyframes clickButton {
            0%, 34% { transform: scale(1); filter: brightness(1); }
            37% { transform: scale(0.94); filter: brightness(0.9); }
            40%, 100% { transform: scale(1); filter: brightness(1); }
        }

        /* 5. Layar Menu Utama / Dashboard (Muncul setelah detik ke-4) */
        .animate-main-screen {
            animation: mainScreenAnim 8s infinite ease-in-out;
        }
        @keyframes mainScreenAnim {
            0%, 38% { opacity: 0; visibility: hidden; }
            42%, 95% { opacity: 1; visibility: visible; }
            100% { opacity: 0; visibility: hidden; }
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-slate-50 via-gray-50 to-blue-50 font-sans antialiased min-h-screen relative overflow-x-hidden text-slate-800">

    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-amber-500/5 rounded-full blur-3xl"></div>
    </div>

    <nav class="w-full fixed top-0 left-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 font-bold text-xl text-slate-900 tracking-tight hover:opacity-90 transition">
                <div class="bg-blue-600 p-2 rounded-lg text-sm flex items-center justify-center shadow-md shadow-blue-600/20">
                    <i class="fa-solid fa-wallet text-white"></i>
                </div>
                <span>E-Tabungan<span class="text-blue-600">.</span></span>
            </a>

            <div class="hidden md:flex items-center gap-5">
                <a href="/harga-paket" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Harga Paket</a>
                <a href="/download-app-landing" class="text-sm font-semibold text-blue-600">Download App</a>
                <div class="h-4 w-px bg-slate-200"></div>
                <a href="/login" class="px-4 py-2 text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl border border-slate-200 transition duration-200 shadow-sm">
                    Masuk / Daftar
                </a>
            </div>

            <div class="flex items-center gap-3 md:hidden">
                <a href="/login" class="px-3 py-1.5 text-sm font-bold text-slate-700 bg-slate-100 rounded-xl border border-slate-200 shadow-sm">
                    Masuk / Daftar
                </a>
                <button id="hamburger-btn" class="text-slate-600 hover:text-blue-600 p-2 focus:outline-none transition">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-slate-200 px-6 py-4 space-y-1 shadow-lg">
            <a href="/harga-paket" class="block text-sm font-bold text-slate-600 hover:text-blue-600 py-2.5 border-b border-slate-50">Harga Paket</a>
            <a href="/download-app-landing" class="block text-sm font-bold text-blue-600 py-2.5">Download App</a>
        </div>
    </nav>


    <div class="hidden md:block">
        <section class="max-w-5xl mx-auto px-4 pt-32 pb-16">
            <div class="grid grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-semibold mb-6 tracking-wide uppercase shadow-sm">
                        <i class="fa-solid fa-mobile-screen-button text-xs"></i> Aplikasi Mobile Guru
                    </div>
                    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-5">
                        Input Tabungan Siswa, Langsung dari Genggaman
                    </h1>
                    <p class="text-lg text-slate-600 leading-relaxed mb-8">
                        Aplikasi E-Tabungan untuk guru memudahkan pencatatan setoran dan penarikan tabungan siswa kapan saja, tanpa perlu membuka laptop. Tersedia untuk perangkat Android.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 mb-6">
                        <a href="https://etabungan-tab2one.hopto.org/apps/E-TabunganV1.0.apk"
                            class="group inline-flex items-center justify-center gap-3 px-6 py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl transition-all duration-200 shadow-lg shadow-slate-900/20 transform hover:-translate-y-0.5">
                            <i class="fa-brands fa-android text-2xl text-emerald-400"></i>
                            <span class="text-left">
                                <span class="block text-[10px] text-slate-300 uppercase tracking-wide">Unduh untuk</span>
                                <span class="block text-sm">Android (.apk)</span>
                            </span>
                        </a>
                    </div>
                    <p class="text-xs text-slate-400">
                        <i class="fa-solid fa-circle-info mr-1"></i> Versi 1.0 &middot; Pastikan mengizinkan instalasi dari sumber tidak dikenal di pengaturan Android Anda.
                    </p>
                </div>

                <div class="flex justify-center">
                    <div class="relative w-60 aspect-[9/19]">
                        <div class="absolute -inset-6 bg-gradient-to-br from-blue-200/40 to-amber-200/40 rounded-[2.5rem] blur-2xl"></div>
                        
                        <div class="absolute inset-0 bg-slate-900 rounded-[2rem] p-2 shadow-2xl border-2 border-slate-800">
                            
                            <div class="relative w-full h-full bg-slate-50 rounded-[1.4rem] overflow-hidden">
                                
                                <div class="absolute inset-0 bg-white p-5 flex flex-col justify-center z-10 animate-login-screen text-slate-800">
                                    <div class="text-center mb-6">
                                        <div class="bg-blue-600 w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-2 text-white shadow-md">
                                            <i class="fa-solid fa-wallet text-sm"></i>
                                        </div>
                                        <h4 class="font-bold text-sm text-slate-900">E-Tabungan</h4>
                                        <p class="text-[10px] text-slate-400">Aplikasi Mitra Guru</p>
                                    </div>
                                    <div class="space-y-3 mb-5">
                                        <div>
                                            <label class="text-[9px] font-bold text-slate-400 block mb-1">Username</label>
                                            <div class="border border-slate-200 rounded-lg p-2 h-8 flex items-center bg-slate-50 relative overflow-hidden">
                                                <span class="text-[11px] font-semibold text-slate-700 animate-type-user">guru_budi</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-[9px] font-bold text-slate-400 block mb-1">Password</label>
                                            <div class="border border-slate-200 rounded-lg p-2 h-8 flex items-center bg-slate-50 relative overflow-hidden">
                                                <span class="text-[11px] font-bold text-slate-800 tracking-widest animate-type-pass">••••••</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="w-full bg-blue-600 text-white font-bold text-xs py-2.5 rounded-lg shadow-md shadow-blue-600/10 animate-click-btn">
                                        Masuk
                                    </button>
                                </div>

                                <div class="absolute inset-0 bg-slate-50 flex flex-col justify-between z-0 animate-main-screen text-slate-800">
                                    <div class="bg-blue-600 px-4 pt-5 pb-3.5 text-white shadow-sm">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center text-[10px]"><i class="fa-solid fa-user"></i></div>
                                                <span class="text-[10px] font-bold">Pak Budi (Guru)</span>
                                            </div>
                                            <i class="fa-solid fa-bell text-[10px] text-blue-100"></i>
                                        </div>
                                        <div class="bg-white/10 rounded-lg p-2 backdrop-blur-sm">
                                            <p class="text-[8px] text-blue-100">Total Tabungan Kelas IV-A</p>
                                            <p class="text-xs font-black">Rp 14.520.000</p>
                                        </div>
                                    </div>
                                    <div class="p-3 space-y-2.5 flex-1 overflow-y-auto">
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Aktivitas Utama</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="bg-white p-2 rounded-xl border border-slate-100 shadow-sm flex items-center gap-1.5">
                                                <div class="w-5 h-5 bg-emerald-100 text-emerald-600 rounded-md flex items-center justify-center text-[9px]"><i class="fa-solid fa-arrow-down"></i></div>
                                                <span class="text-[9px] font-bold text-slate-700">Setor</span>
                                            </div>
                                            <div class="bg-white p-2 rounded-xl border border-slate-100 shadow-sm flex items-center gap-1.5">
                                                <div class="w-5 h-5 bg-rose-100 text-rose-600 rounded-md flex items-center justify-center text-[9px]"><i class="fa-solid fa-arrow-up"></i></div>
                                                <span class="text-[9px] font-bold text-slate-700">Tarik</span>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-xl p-2 border border-slate-100 shadow-sm space-y-1.5">
                                            <p class="text-[8px] font-bold text-slate-400">Riwayat Singkat</p>
                                            <div class="flex justify-between items-center text-[8px] border-b border-slate-50 pb-1">
                                                <span class="font-medium text-slate-700">Ahmad Fauzi</span>
                                                <span class="text-emerald-600 font-bold">+20k</span>
                                            </div>
                                            <div class="flex justify-between items-center text-[8px]">
                                                <span class="font-medium text-slate-700">Siti Aminah</span>
                                                <span class="text-rose-600 font-bold">-50k</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white border-t border-slate-200 h-11 grid grid-cols-3 items-center text-center">
                                        <div class="text-blue-600 flex flex-col items-center justify-center">
                                            <i class="fa-solid fa-house text-[10px]"></i>
                                            <span class="text-[7.5px] font-bold mt-0.5">Beranda</span>
                                        </div>
                                        <div class="text-slate-400 flex flex-col items-center justify-center">
                                            <i class="fa-solid fa-money-bill-transfer text-[10px]"></i>
                                            <span class="text-[7.5px] font-medium mt-0.5">Transaksi</span>
                                        </div>
                                        <div class="text-slate-400 flex flex-col items-center justify-center">
                                            <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                                            <span class="text-[7.5px] font-medium mt-0.5">Riwayat</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-5xl mx-auto px-4 py-12 mb-12">
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight text-center mb-10">
                Cara Instalasi
            </h2>
            <div class="grid grid-cols-3 gap-6">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center shadow-sm">
                    <div class="w-10 h-10 mx-auto bg-blue-100 text-blue-600 font-bold rounded-full flex items-center justify-center mb-4">1</div>
                    <h3 class="font-bold text-slate-800 text-sm mb-2">Unduh File APK</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Klik tombol unduh di atas menggunakan browser Android Anda.</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center shadow-sm">
                    <div class="w-10 h-10 mx-auto bg-blue-100 text-blue-600 font-bold rounded-full flex items-center justify-center mb-4">2</div>
                    <h3 class="font-bold text-slate-800 text-sm mb-2">Izinkan Instalasi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Aktifkan "Izinkan dari sumber ini" jika muncul peringatan keamanan.</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center shadow-sm">
                    <div class="w-10 h-10 mx-auto bg-blue-100 text-blue-600 font-bold rounded-full flex items-center justify-center mb-4">3</div>
                    <h3 class="font-bold text-slate-800 text-sm mb-2">Login & Mulai</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Gunakan akun guru yang sudah didaftarkan oleh admin sekolah Anda.</p>
                </div>
            </div>
        </section>
    </div>


    <div class="block md:hidden px-4 pt-28 pb-12 space-y-8">
        
        <div class="text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-semibold mb-3 uppercase tracking-wide shadow-sm">
                <i class="fa-solid fa-mobile-screen-button text-xs"></i> Aplikasi Mobile Guru
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Input Tabungan Siswa
            </h1>
        </div>

        <div class="bg-slate-100/70 border border-slate-200/80 rounded-2xl p-5 shadow-inner">
            <h2 class="text-base font-extrabold text-slate-900 tracking-tight text-center mb-4 flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-info text-blue-600"></i> Panduan Cara Instalasi
            </h2>
            <div class="space-y-3">
                <div class="bg-white border border-slate-200 rounded-xl p-3 flex items-start gap-3 shadow-sm">
                    <div class="w-6 h-6 bg-blue-600 text-white font-bold rounded-full flex items-center justify-center text-xs shrink-0 shadow-sm">1</div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-xs mb-0.5">Unduh Berkas APK</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Tekan tombol download yang ada di bawah halaman ini.</p>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-3 flex items-start gap-3 shadow-sm">
                    <div class="w-6 h-6 bg-blue-600 text-white font-bold rounded-full flex items-center justify-center text-xs shrink-0 shadow-sm">2</div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-xs mb-0.5">Izinkan Sumber Tidak Dikenal</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Jika muncul peringatan keamanan sistem Android, pilih "Izinkan Instalasi dari Sumber Ini".</p>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-3 flex items-start gap-3 shadow-sm">
                    <div class="w-6 h-6 bg-blue-600 text-white font-bold rounded-full flex items-center justify-center text-xs shrink-0 shadow-sm">3</div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-xs mb-0.5">Buka & Masuk</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Setelah sukses terpasang, login menggunakan akun guru Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center py-2">
            <div class="relative w-48 aspect-[9/19]">
                <div class="absolute -inset-4 bg-gradient-to-br from-blue-200/30 to-amber-200/30 rounded-[2.5rem] blur-xl"></div>
                
                <div class="absolute inset-0 bg-slate-900 rounded-[2rem] p-2 shadow-lg border-2 border-slate-800">
                    
                    <div class="relative w-full h-full bg-slate-50 rounded-[1.4rem] overflow-hidden">
                        
                        <div class="absolute inset-0 bg-white p-4 flex flex-col justify-center z-10 animate-login-screen text-slate-800">
                            <div class="text-center mb-4">
                                <div class="bg-blue-600 w-8 h-8 rounded-lg flex items-center justify-center mx-auto mb-1.5 text-white">
                                    <i class="fa-solid fa-wallet text-xs"></i>
                                </div>
                                <h4 class="font-bold text-xs text-slate-900">E-Tabungan</h4>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div>
                                    <div class="border border-slate-200 rounded-md p-1.5 h-7 flex items-center bg-slate-50 relative overflow-hidden">
                                        <span class="text-[9px] font-semibold text-slate-600 animate-type-user">guru_budi</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="border border-slate-200 rounded-md p-1.5 h-7 flex items-center bg-slate-50 relative overflow-hidden">
                                        <span class="text-[9px] font-bold text-slate-700 tracking-widest animate-type-pass">••••••</span>
                                    </div>
                                </div>
                            </div>
                            <button class="w-full bg-blue-600 text-white font-bold text-[10px] py-1.5 rounded-md animate-click-btn">
                                Masuk
                            </button>
                        </div>

                        <div class="absolute inset-0 bg-slate-50 flex flex-col justify-between z-0 animate-main-screen text-slate-800">
                            <div class="bg-blue-600 px-3 pt-4 pb-2.5 text-white shadow-sm">
                                <p class="text-[8px] text-blue-100">Pak Budi (Guru)</p>
                                <p class="text-[11px] font-black">Rp 14.520.000</p>
                            </div>
                            <div class="p-2 space-y-2 flex-1 overflow-y-auto">
                                <div class="grid grid-cols-2 gap-1.5">
                                    <div class="bg-white p-1.5 rounded-lg border border-slate-100 shadow-sm flex items-center gap-1">
                                        <div class="w-4 h-4 bg-emerald-100 text-emerald-600 rounded-sm flex items-center justify-center text-[8px]"><i class="fa-solid fa-arrow-down"></i></div>
                                        <span class="text-[8px] font-bold text-slate-700">Setor</span>
                                    </div>
                                    <div class="bg-white p-1.5 rounded-lg border border-slate-100 shadow-sm flex items-center gap-1">
                                        <div class="w-4 h-4 bg-rose-100 text-rose-600 rounded-sm flex items-center justify-center text-[8px]"><i class="fa-solid fa-arrow-up"></i></div>
                                        <span class="text-[8px] font-bold text-slate-700">Tarik</span>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-1.5 border border-slate-100 shadow-sm space-y-1">
                                    <div class="flex justify-between items-center text-[7px] border-b border-slate-50 pb-0.5">
                                        <span class="text-slate-600">Ahmad Fauzi</span>
                                        <span class="text-emerald-600 font-bold">+20k</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[7px]">
                                        <span class="text-slate-600">Siti Aminah</span>
                                        <span class="text-rose-600 font-bold">-50k</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border-t border-slate-200 h-9 grid grid-cols-3 items-center text-center">
                                <div class="text-blue-600 flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-house text-[8px]"></i>
                                    <span class="text-[6.5px] font-bold mt-0.5">Beranda</span>
                                </div>
                                <div class="text-slate-400 flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-money-bill-transfer text-[8px]"></i>
                                    <span class="text-[6.5px] font-medium mt-0.5">Transaksi</span>
                                </div>
                                <div class="text-slate-400 flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-clock-rotate-left text-[8px]"></i>
                                    <span class="text-[6.5px] font-medium mt-0.5">Riwayat</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 text-center shadow-md">
            <span class="block text-xs font-bold text-blue-600 tracking-wide uppercase mb-3">Klik tombol di bawah untuk mengunduh berkas</span>
            <a href="https://etabungan-tab2one.hopto.org/apps/E-TabunganV1.0.apk"
                class="w-full inline-flex items-center justify-center gap-3 px-5 py-3.5 bg-slate-900 active:bg-slate-800 text-white font-bold rounded-xl transition shadow-md">
                <i class="fa-brands fa-android text-xl text-emerald-400"></i>
                <span class="text-left">
                    <span class="block text-[9px] text-slate-300 uppercase tracking-wider">Unduh Sekarang</span>
                    <span class="block text-xs font-bold">Download Android (.apk)</span>
                </span>
            </a>
            <div class="mt-3 text-[11px] text-slate-400 flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-emerald-500"></i> Versi Resmi 1.0 &middot; Aman & Ringan
            </div>
        </div>

    </div>


    <footer class="w-full text-center py-8 text-xs text-slate-400 border-t border-slate-100">
        &copy; {{ date('Y') }} E-Tabungan. Hak Cipta Dilindungi.
    </footer>

    <script>
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        hamburgerBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>

</body>
</html>