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

            <div class="flex items-center gap-5">
                <a href="/harga-paket" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition hidden sm:inline-block">Harga Paket</a>
                <a href="/download-app" class="text-sm font-semibold text-blue-600 hidden sm:inline-block">Download App</a>
                <div class="h-4 w-px bg-slate-200 hidden sm:inline-block"></div>
                <a href="/login" class="px-4 py-2 text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl border border-slate-200 transition duration-200 shadow-sm">
                    Masuk / Daftar
                </a>
            </div>
        </div>
    </nav>

    <section class="max-w-5xl mx-auto px-4 pt-32 pb-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            <!-- Kolom Kiri: Teks & CTA -->
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-semibold mb-6 tracking-wide uppercase shadow-sm">
                    <i class="fa-solid fa-mobile-screen-button text-xs"></i> Aplikasi Mobile Guru
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-5">
                    Input Tabungan Siswa, Langsung dari Genggaman
                </h1>

                <p class="text-base sm:text-lg text-slate-600 leading-relaxed mb-8">
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
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Versi 1.0 &middot; Pastikan mengizinkan instalasi dari sumber tidak dikenal di pengaturan Android Anda.
                </p>
            </div>

            <!-- Kolom Kanan: Mockup Visual -->
            <div class="flex justify-center">
                <div class="relative w-64">
                    <div class="absolute -inset-6 bg-gradient-to-br from-blue-200/40 to-amber-200/40 rounded-[3rem] blur-2xl"></div>
                    <div class="relative bg-slate-900 rounded-[2.5rem] p-3 shadow-2xl border-4 border-slate-800">
                        <div class="bg-white rounded-[1.8rem] overflow-hidden aspect-[9/19] flex flex-col">
                            <div class="bg-blue-600 px-4 py-5 flex items-center gap-2">
                                <i class="fa-solid fa-wallet text-white text-sm"></i>
                                <span class="text-white font-bold text-xs">E-Tabungan Guru</span>
                            </div>
                            <div class="p-4 space-y-3 flex-1 bg-slate-50">
                                <div class="bg-white rounded-xl p-3 shadow-sm border border-slate-100">
                                    <div class="h-2 w-16 bg-slate-200 rounded-full mb-2"></div>
                                    <div class="h-2 w-24 bg-slate-100 rounded-full"></div>
                                </div>
                                <div class="bg-white rounded-xl p-3 shadow-sm border border-slate-100">
                                    <div class="h-2 w-20 bg-slate-200 rounded-full mb-2"></div>
                                    <div class="h-2 w-12 bg-slate-100 rounded-full"></div>
                                </div>
                                <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-100">
                                    <div class="h-2 w-14 bg-emerald-200 rounded-full mb-2"></div>
                                    <div class="h-2 w-20 bg-emerald-100 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Langkah Instalasi -->
    <section class="max-w-4xl mx-auto px-4 py-16">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight text-center mb-10">
            Cara Instalasi
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

    <footer class="w-full text-center py-8 text-xs text-slate-400 border-t border-slate-100">
        &copy; {{ date('Y') }} E-Tabungan. Hak Cipta Dilindungi.
    </footer>

</body>
</html>