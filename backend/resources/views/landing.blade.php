<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Tabungan Sekolah - Solusi Digital Pencatatan Tabungan Siswa</title>

    <link rel="icon" type="image/png" sizes="32x32" href="/favicon_b.png">


    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        html, body { overflow-x: hidden; width: 100%; }
        @keyframes slideUp {
            0%, 20% { transform: translateY(0); }
            25%, 45% { transform: translateY(-25%); }
            50%, 70% { transform: translateY(-50%); }
            75%, 95% { transform: translateY(-75%); }
            100% { transform: translateY(0); }
        }
        .animate-slide-text {
            animation: slideUp 8s cubic-bezier(0.68, -0.6, 0.32, 1.6) infinite;
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
            <a href="#" class="flex items-center gap-2.5 font-bold text-xl text-slate-900 tracking-tight hover:opacity-90 transition">
                <div class="bg-blue-600 p-2 rounded-lg text-sm flex items-center justify-center shadow-md shadow-blue-600/20">
                    <i class="fa-solid fa-wallet text-white"></i>
                </div>
                <span>E-Tabungan<span class="text-blue-600">.</span></span>
            </a>

            <div class="flex items-center gap-5">
                <a href="/harga-paket" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition hidden sm:inline-block">Harga Paket</a>
                <div class="h-4 w-px bg-slate-200 hidden sm:inline-block"></div>
                <a href="/login" class="px-4 py-2 text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl border border-slate-200 transition duration-200 shadow-sm">
                    Masuk / Daftar
                </a>
            </div>
        </div>
    </nav>

    <section class="min-h-screen flex flex-col items-center justify-center px-4 pt-10 text-center relative">
        <div class="max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-semibold mb-6 tracking-wide uppercase shadow-sm">
                <i class="fa-solid fa-mobile-screen-button text-xs"></i> Sistem Input Tabungan Digital
            </div>
            
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                Cara Modern Kelola <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Tabungan Siswa </span>Tanpa Ribet Buku Fisik<br class="hidden sm:inline" />
                <span class="inline-block relative overflow-hidden text-center align-bottom h-[1.2em] w-[180px] sm:w-[240px] text-blue-600">
                    <span class="absolute block animate-slide-text left-0 right-0 mx-auto top-0 w-full text-center">
                        <span class="block h-[1.2em] leading-normal font-black">Mudah.</span>
                        <span class="block h-[1.2em] leading-normal font-black text-emerald-600">Praktis.</span>
                        <span class="block h-[1.2em] leading-normal font-black text-indigo-600">Efisien.</span>
                        <span class="block h-[1.2em] leading-normal font-black text-amber-600">Aman.</span>
                    </span>
                </span>
            </h1>
            
            <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                Catat tabungan siswa jadi super cepat langsung dari aplikasi. Tinggalkan cara lama, beralih ke ekosistem digital sekarang!
            </p>
            
            <div class="flex justify-center">
                <a href="#paket-layanan" class="group px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg rounded-2xl transition-all duration-200 shadow-xl shadow-blue-600/20 flex items-center gap-3 transform hover:-translate-y-1">
                    Coba Sekarang Gratis
                    <i class="fa-solid fa-arrow-down transition-transform group-hover:translate-y-1"></i>
                </a>
            </div>
        </div>
    </section>

    <section id="paket-layanan" class="max-w-6xl mx-auto px-4 py-24 scroll-mt-132">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                Pilih Paket Pengelolaan E-Tabungan
            </h2>
            <p class="text-base md:text-lg text-slate-500 max-w-2xl mx-auto">
                Sesuaikan kapasitas input aktif dan jangkauan koordinasi kelas dengan skala kebutuhan sekolah Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch pt-4">
            
            <div class="bg-white border border-slate-200 rounded-2xl p-8 flex flex-col items-center shadow-sm relative transition-all duration-300 transform hover:scale-105 hover:shadow-md hover:border-slate-300">
                <div class="p-3 bg-amber-50 rounded-full mb-4 border border-amber-100">
                    <i class="fa-solid fa-trophy text-amber-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Bronze Tier</h3>
                <span class="text-3xl font-extrabold text-slate-900 my-4">Gratis</span>
                <p class="text-xs text-slate-400 text-center mb-4">Uji coba pencatatan awal untuk sekolah skala kecil</p>
                
                <div class="w-full h-px bg-slate-100 my-2"></div>
                
                <ul class="w-full space-y-3.5 my-6 text-slate-600 flex-grow text-sm">
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Maksimal 10 Nama Siswa Aktif</li>
                    <li class="flex items-center font-semibold text-amber-600"><i class="fa-solid fa-circle-check text-amber-500 mr-3 text-base"></i> Total 2 Akun (1 Admin & 1 Akun Guru)</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Hanya 1 Kelas Kelolaan Guru</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Maksimal 5 Input Transaksi / Hari</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Atur Status Aktif/Non-Aktif Siswa</li>
                    <li class="flex items-center text-slate-400 line-through"><i class="fa-solid fa-circle-xmark text-slate-300 mr-3 text-base"></i> WhatsApp Notifikasi Orang Tua</li>
                    <li class="flex items-center text-slate-400 line-through"><i class="fa-solid fa-circle-xmark text-slate-300 mr-3 text-base"></i> Tambah Akun Guru Tanpa Batas</li>
                    <li class="flex items-center text-slate-400 line-through"><i class="fa-solid fa-circle-xmark text-slate-300 mr-3 text-base"></i> Guru Mengelola Banyak Kelas</li>
                </ul>
                
                <a href="{{ route('login') }}" class="w-full text-center bg-slate-800 hover:bg-slate-900 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-sm">
                    Mulai Gratis
                </a>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-8 flex flex-col items-center shadow-sm relative transition-all duration-300 transform hover:scale-105 hover:shadow-md hover:border-blue-400">
                <div class="p-3 bg-blue-50 rounded-full mb-4 border border-blue-100">
                    <i class="fa-solid fa-bookmark text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Silver Tier</h3>
                <span class="text-3xl font-extrabold text-slate-900 my-4">Rp 150k<span class="text-sm font-normal text-slate-400">/bln</span></span>
                <p class="text-xs text-slate-400 text-center mb-4">Solusi ideal untuk koordinasi guru antar kelas</p>
                
                <div class="w-full h-px bg-slate-100 my-2"></div>
                
                <ul class="w-full space-y-3.5 my-6 text-slate-600 flex-grow text-sm">
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Maksimal 300 Nama Siswa Aktif</li>
                    <li class="flex items-center font-semibold text-blue-600"><i class="fa-solid fa-circle-check text-blue-500 mr-3 text-base"></i> Tambah Akun Guru Tanpa Batas</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Guru Bisa Kelola Banyak Kelas</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Maksimal 150 Input Transaksi / Hari</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Atur Status Aktif/Non-Aktif Siswa</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> WhatsApp Notifikasi ke Orang Tua</li>
                    <li class="flex items-center text-slate-400 line-through"><i class="fa-solid fa-circle-xmark text-slate-300 mr-3 text-base"></i> Download Rekap Buku Tabungan (PDF/Excel)</li>
                    <li class="flex items-center text-slate-400 line-through"><i class="fa-solid fa-circle-xmark text-slate-300 mr-3 text-base"></i> Import Excel Saldo Awal Siswa</li>
                </ul>
                
                <a href="{{ route('login') }}" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-md shadow-blue-600/10">
                    Pilih Silver
                </a>
            </div>

            <div class="bg-white border-2 border-amber-400 rounded-2xl p-8 flex flex-col items-center shadow-md relative transform md:-translate-y-4 transition-all duration-300 hover:scale-110 hover:shadow-lg hover:border-amber-500">
                <div class="absolute -top-3 bg-gradient-to-r from-amber-400 to-yellow-400 text-slate-950 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm">
                    Paling Populer
                </div>
                <div class="p-3 bg-amber-50 rounded-full mb-4 mt-2 border border-amber-100">
                    <i class="fa-solid fa-star text-amber-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Golden Tier</h3>
                <span class="text-3xl font-extrabold text-slate-900 my-4">Rp 350k<span class="text-sm font-normal text-slate-400">/bln</span></span>
                <p class="text-xs text-slate-400 text-center mb-4">Akses penuh tanpa batas untuk pengelolaan tabungan maksimal</p>
                
                <div class="w-full h-px bg-slate-100 my-2"></div>
                
                <ul class="w-full space-y-3.5 my-6 text-slate-600 flex-grow text-sm">
                    <li class="flex items-center font-semibold text-amber-600"><i class="fa-solid fa-circle-check text-amber-500 mr-3 text-base"></i> Kuota Nama Siswa Tanpa Batas</li>
                    <li class="flex items-center font-semibold text-amber-600"><i class="fa-solid fa-circle-check text-amber-500 mr-3 text-base"></i> Tambah Akun Guru Tanpa Batas</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Guru Bisa Kelola Banyak Kelas</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Input Transaksi Harian Tanpa Batas</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> Atur Status Aktif/Non-Aktif Siswa</li>
                    <li class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-base"></i> WhatsApp Notifikasi ke Orang Tua</li>
                    <li class="flex items-center font-semibold text-blue-600"><i class="fa-solid fa-circle-check text-blue-500 mr-3 text-base"></i> Download Rekap Buku Tabungan (PDF/Excel)</li>
                    <li class="flex items-center font-semibold text-blue-600"><i class="fa-solid fa-circle-check text-blue-500 mr-3 text-base"></i> Import Excel Saldo Awal Siswa</li>
                </ul>
                
                <a href="{{ route('login') }}" class="w-full text-center bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold py-3 px-4 rounded-xl transition-all duration-200 shadow-md shadow-amber-500/10">
                    Pilih Golden
                </a>
            </div>

        </div>
    </section>

</body>
</html>