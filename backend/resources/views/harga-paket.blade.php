<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harga Paket - E-Tabungan Sekolah</title>

    <link class="flex items-center" rel="icon" type="image/x-icon" href="/favicon_ico.png">
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

            <div class="hidden md:flex items-center gap-5">
                <a href="/harga-paket" class="text-sm font-semibold text-blue-600">Harga Paket</a>
                <a href="/download-app-landing" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Download App</a>
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
            <a href="/harga-paket" class="block text-sm font-bold text-blue-600 py-2.5 border-b border-slate-50">Harga Paket</a>
            <a href="/download-app-landing" class="block text-sm font-bold text-slate-600 hover:text-blue-600 py-2.5">Download App</a>
        </div>
    </nav>

    <section class="max-w-6xl mx-auto px-4 pt-32 pb-12 text-center relative">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-semibold mb-6 tracking-wide uppercase shadow-sm">
            <i class="fa-solid fa-tags text-xs"></i> Paket Berlangganan
        </div>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
            Harga Jelas, Tanpa Biaya Tersembunyi
        </h1>
        <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed font-medium">
            Bandingkan fitur tiap paket secara detail, lalu pilih yang paling sesuai dengan skala sekolah Anda.
        </p>
    </section>

    <section class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">

            <div class="bg-white border border-slate-200 rounded-2xl p-8 flex flex-col items-center shadow-sm transform transition-all duration-300 hover:-translate-y-2 hover:scale-[1.03] hover:shadow-lg">
                <div class="p-3 bg-amber-50 rounded-full mb-4 border border-amber-100">
                    <i class="fa-solid fa-trophy text-amber-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Bronze Tier</h3>
                <span class="text-3xl font-extrabold text-slate-900 my-4">Gratis</span>
                <p class="text-xs text-slate-400 text-center mb-4">Uji coba pencatatan awal untuk sekolah skala kecil</p>
                <a href="{{ route('register') }}?paket=BRONZE" class="w-full text-center bg-slate-800 hover:bg-slate-900 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-sm mt-auto">
                    Mulai Gratis
                </a>
            </div>

            <div class="bg-white border-2 border-blue-300 rounded-2xl p-8 flex flex-col items-center shadow-md transform transition-all duration-300 hover:-translate-y-2 hover:scale-[1.03] hover:shadow-xl hover:border-blue-400">
                <div class="p-3 bg-blue-50 rounded-full mb-4 border border-blue-100">
                    <i class="fa-solid fa-bookmark text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Silver Tier</h3>
                <span class="text-3xl font-extrabold text-slate-900 my-4">Rp 150k<span class="text-sm font-normal text-slate-400">/bln</span></span>
                <p class="text-xs text-slate-400 text-center mb-4">Solusi ideal untuk koordinasi guru antar kelas</p>
                <a href="{{ route('register') }}?paket=SILVER" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-md shadow-blue-600/10 mt-auto">
                    Pilih Silver
                </a>
            </div>

            <div class="bg-white border-2 border-amber-400 rounded-2xl p-8 flex flex-col items-center shadow-md relative transform transition-all duration-300 hover:-translate-y-2 hover:scale-[1.03] hover:shadow-xl hover:border-amber-500">
                <div class="absolute -top-3 bg-gradient-to-r from-amber-400 to-yellow-400 text-slate-950 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm">
                    Paling Populer
                </div>
                <div class="p-3 bg-amber-50 rounded-full mb-4 mt-2 border border-amber-100">
                    <i class="fa-solid fa-star text-amber-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Golden Tier</h3>
                <span class="text-3xl font-extrabold text-slate-900 my-4">Rp 350k<span class="text-sm font-normal text-slate-400">/bln</span></span>
                <p class="text-xs text-slate-400 text-center mb-4">Akses penuh tanpa batas untuk pengelolaan maksimal</p>
                <a href="{{ route('register') }}?paket=GOLDEN" class="w-full text-center bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold py-3 px-4 rounded-xl transition-all duration-200 shadow-md shadow-amber-500/10 mt-auto">
                    Pilih Golden
                </a>
            </div>

        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 py-16">
        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight text-center mb-10">
            Perbandingan Fitur Lengkap
        </h2>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left p-5 font-bold text-slate-500 uppercase text-xs tracking-wider w-2/5">Fitur</th>
                            <th class="text-center p-5 font-bold text-slate-700 w-1/5">
                                <div class="flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-trophy text-amber-600"></i> Bronze
                                </div>
                            </th>
                            <th class="text-center p-5 font-bold text-blue-700 w-1/5 bg-blue-50/40">
                                <div class="flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-bookmark text-blue-600"></i> Silver
                                </div>
                            </th>
                            <th class="text-center p-5 font-bold text-amber-700 w-1/5 bg-amber-50/40">
                                <div class="flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-star text-amber-500"></i> Golden
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-5 text-slate-600 font-medium">Kuota Nama Siswa Aktif</td>
                            <td class="p-5 text-center text-slate-500">10</td>
                            <td class="p-5 text-center text-slate-700 font-semibold bg-blue-50/20">300</td>
                            <td class="p-5 text-center text-amber-700 font-bold bg-amber-50/20">Tanpa Batas</td>
                        </tr>
                        <tr>
                            <td class="p-5 text-slate-600 font-medium">Jumlah Akun Guru</td>
                            <td class="p-5 text-center text-slate-500">1 Akun</td>
                            <td class="p-5 text-center text-blue-700 font-bold bg-blue-50/20">Tanpa Batas</td>
                            <td class="p-5 text-center text-amber-700 font-bold bg-amber-50/20">Tanpa Batas</td>
                        </tr>
                        <tr>
                            <td class="p-5 text-slate-600 font-medium">Kelas Kelolaan per Guru</td>
                            <td class="p-5 text-center text-slate-500">1 Kelas</td>
                            <td class="p-5 text-center text-slate-700 font-semibold bg-blue-50/20">Banyak Kelas</td>
                            <td class="p-5 text-center text-slate-700 font-semibold bg-amber-50/20">Banyak Kelas</td>
                        </tr>
                        <tr>
                            <td class="p-5 text-slate-600 font-medium">Input Transaksi / Hari</td>
                            <td class="p-5 text-center text-slate-500">5x</td>
                            <td class="p-5 text-center text-slate-700 font-semibold bg-blue-50/20">150x</td>
                            <td class="p-5 text-center text-amber-700 font-bold bg-amber-50/20">Tanpa Batas</td>
                        </tr>
                        <tr>
                            <td class="p-5 text-slate-600 font-medium">Atur Status Aktif/Non-Aktif Siswa</td>
                            <td class="p-5 text-center"><i class="fa-solid fa-check text-emerald-500"></i></td>
                            <td class="p-5 text-center bg-blue-50/20"><i class="fa-solid fa-check text-emerald-500"></i></td>
                            <td class="p-5 text-center bg-amber-50/20"><i class="fa-solid fa-check text-emerald-500"></i></td>
                        </tr>
                        <tr>
                            <td class="p-5 text-slate-600 font-medium">WhatsApp Notifikasi ke Orang Tua</td>
                            <td class="p-5 text-center"><i class="fa-solid fa-xmark text-slate-300"></i></td>
                            <td class="p-5 text-center bg-blue-50/20"><i class="fa-solid fa-check text-emerald-500"></i></td>
                            <td class="p-5 text-center bg-amber-50/20"><i class="fa-solid fa-check text-emerald-500"></i></td>
                        </tr>
                        <tr>
                            <td class="p-5 text-slate-600 font-medium">Download Rekap Buku Tabungan (PDF/Excel)</td>
                            <td class="p-5 text-center"><i class="fa-solid fa-xmark text-slate-300"></i></td>
                            <td class="p-5 text-center bg-blue-50/20"><i class="fa-solid fa-xmark text-slate-300"></i></td>
                            <td class="p-5 text-center bg-amber-50/20"><i class="fa-solid fa-check text-emerald-500"></i></td>
                        </tr>
                        <tr>
                            <td class="p-5 text-slate-600 font-medium">Import Excel Saldo Awal Siswa</td>
                            <td class="p-5 text-center"><i class="fa-solid fa-xmark text-slate-300"></i></td>
                            <td class="p-5 text-center bg-blue-50/20"><i class="fa-solid fa-xmark text-slate-300"></i></td>
                            <td class="p-5 text-center bg-amber-50/20"><i class="fa-solid fa-check text-emerald-500"></i></td>
                        </tr>
                        <tr>
                            <td class="p-5 text-slate-600 font-medium">Aplikasi Mobile Guru (Android)</td>
                            <td class="p-5 text-center"><i class="fa-solid fa-check text-emerald-500"></i></td>
                            <td class="p-5 text-center bg-blue-50/20"><i class="fa-solid fa-check text-emerald-500"></i></td>
                            <td class="p-5 text-center bg-amber-50/20"><i class="fa-solid fa-check text-emerald-500"></i></td>
                        </tr>
                        <tr>
                            <td class="p-5 text-slate-700 font-bold">Harga</td>
                            <td class="p-5 text-center text-slate-700 font-bold">Gratis</td>
                            <td class="p-5 text-center text-blue-700 font-bold bg-blue-50/20">Rp 150.000/bln</td>
                            <td class="p-5 text-center text-amber-700 font-bold bg-amber-50/20">Rp 350.000/bln</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            Semua harga sudah termasuk akses penuh ke aplikasi web admin dan aplikasi mobile guru. Tidak ada biaya setup atau biaya tersembunyi lainnya.
        </p>
    </section>

    <section class="max-w-4xl mx-auto px-4 py-16 text-center">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl p-10 sm:p-12 shadow-xl shadow-blue-600/20">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mb-3">
                Siap Beralih ke Pencatatan Digital?
            </h2>
            <p class="text-blue-100 mb-8 max-w-xl mx-auto">
                Mulai dengan paket Bronze gratis, upgrade kapan saja sesuai kebutuhan sekolah Anda.
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white hover:bg-slate-50 text-blue-700 font-bold rounded-xl transition-all duration-200 shadow-lg">
                Daftar Sekolah Sekarang <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
        </div>
    </section>

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