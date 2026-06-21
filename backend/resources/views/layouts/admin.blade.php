<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - E-Tabungan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar untuk sidebar agar rapi */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.2);
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-screen">

    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0 lg:hidden"></div>

    <div class="flex min-h-screen">
        
        <aside id="main-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-100 z-50 flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:h-screen">
            
            <div class="flex flex-col h-full overflow-y-auto custom-scrollbar">
                <div class="p-6 flex items-center justify-between border-b border-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-tr from-blue-600 to-indigo-500 p-2.5 rounded-xl text-white shadow-md shadow-blue-500/20">
                            <i class="fa-solid fa-wallet text-sm"></i>
                        </div>
                        <h1 class="text-slate-900 font-extrabold text-sm tracking-wider">E-TABUNGAN</h1>
                    </div>
                    <button id="close-sidebar-btn" class="p-1 text-slate-400 hover:text-slate-600 lg:hidden rounded-lg hover:bg-slate-50">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <nav class="p-4 flex-grow space-y-1.5">
                    <p class="px-4 text-[10px] font-bold tracking-widest text-slate-400 uppercase mb-2">Utama</p>
                    
                    <a href="/dashboard-admin" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->is('dashboard-admin') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-chart-pie text-base w-5"></i> Dashboard
                    </a>
                    
                    <p class="px-4 text-[10px] font-bold tracking-widest text-slate-400 uppercase pt-4 mb-2">Manajemen</p>

                    <a href="/kelas" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->is('kelas') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-layer-group text-base w-5"></i> Manajemen Kelas
                    </a>

                    <a href="/data-guru" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->is('data-guru') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-users text-base w-5"></i> Manajemen Guru
                    </a>

                    <a href="/akun-guru" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->is('akun-guru') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-chalkboard-user text-base w-5"></i> Akun Guru
                    </a>
                    
                    <a href="/transaksi" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->is('transaksi') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-receipt text-base w-5"></i> Log Transaksi
                    </a>

                    <p class="px-4 text-[10px] font-bold tracking-widest text-slate-400 uppercase pt-4 mb-2">Sistem</p>
                    
                    <a href="/pengaturan" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-200 {{ request()->is('pengaturan') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-sliders text-base w-5"></i> Pengaturan
                    </a>
                </nav>

                <div class="p-4 border-t border-slate-50 bg-slate-50/50 mt-auto">
                    <div class="bg-white border border-slate-100 p-3.5 rounded-2xl shadow-xs flex flex-col gap-3">
                        
                        <div class="flex items-center gap-3 min-w-0">
                            <img id="sidebar-avatar" src="https://ui-avatars.com/api/?background=3b82f6&color=fff&name=Admin" class="w-9 h-9 rounded-xl border border-slate-100 object-cover shrink-0">
                            <div class="min-w-0 flex-grow">
                                <p id="txt-sidebar-nama" class="text-xs font-bold text-slate-900 truncate">Admin</p>
                                <p id="txt-sidebar-sekolah" class="text-[10px] font-medium text-slate-400 truncate">Memuat...</p>
                            </div>
                        </div>

                        <button onclick="logout()" class="w-full py-2 px-3 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition duration-200 flex items-center justify-center gap-2 text-xs font-semibold shadow-xs">
                            <i class="fa-solid fa-arrow-right-from-bracket text-[11px]"></i>
                            <span>Keluar Aplikasi</span>
                        </button>
                        
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-grow flex flex-col min-w-0 h-screen overflow-y-auto">
            
            <header class="bg-white border-b border-slate-100 h-16 flex items-center justify-between px-6 shrink-0 lg:hidden">
                <div class="flex items-center gap-3">
                    <button id="open-sidebar-btn" class="p-2 -ml-2 text-slate-500 hover:text-slate-700 hover:bg-slate-50 rounded-xl transition duration-150">
                        <i class="fa-solid fa-bars text-base"></i>
                    </button>
                    <span class="text-xs font-bold text-slate-900 tracking-wide uppercase">@yield('title')</span>
                </div>
                <div class="flex items-center gap-2">
                    <img id="top-navbar-avatar" src="https://ui-avatars.com/api/?background=3b82f6&color=fff&name=Admin" class="w-7 h-7 rounded-lg">
                </div>
            </header>

            <main class="flex-grow p-6 md:p-8 max-w-7xl w-full mx-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Setup rute API global
        window.API_ROUTES = {
            login: '/api/services/tab2one/auth/login',
            logout: '/api/services/tab2one/auth/logout',
            dashboard: '/api/services/tab2one/admin/dashboard',
            postDataGuru : '/api/services/tab2one/admin/guru/data-guru',
            getDataGuru : '/api/services/tab2one/admin/guru/data-guru-list',
            postAkunGuru : '/api/services/tab2one/admin/guru/akun-guru',
            getAkunGuru : '/api/services/tab2one/admin/guru/akun-guru-list',
            resetPasswordGuru : '/api/services/tab2one/admin/guru/reset-password',
            getKelas : '/api/services/tab2one/admin/guru/kelas-admin',
            postKelas : '/api/services/tab2one/admin/guru/kelas',
            getTransaksi : '/api/services/tab2one/admin/guru/transaksi',
            toggleStatusGuru : (id) => `/api/services/tab2one/admin/guru/statusAkunGuru/${id}`,
            resetSesiGuru : (id) => `/api/services/tab2one/admin/guru/resetSesiGuru/${id}`,

        };

        // DOM Elements untuk Drawer Mobile Sidebar
        const mainSidebar = document.getElementById('main-sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const openSidebarBtn = document.getElementById('open-sidebar-btn');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');

        function toggleSidebar() {
            const isHidden = mainSidebar.classList.contains('-translate-x-full');
            if (isHidden) {
                mainSidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                setTimeout(() => {
                    sidebarOverlay.classList.remove('opacity-0');
                }, 20);
            } else {
                mainSidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('opacity-0');
                sidebarOverlay.addEventListener('transitionend', function handler() {
                    sidebarOverlay.classList.add('hidden');
                    sidebarOverlay.removeEventListener('transitionend', handler);
                });
            }
        }

        if(openSidebarBtn) openSidebarBtn.addEventListener('click', toggleSidebar);
        if(closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
        if(sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

        // Eksekusi Pembersihan Sesi saat Log Out
        function logout() {
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Apakah Anda yakin ingin keluar dari sistem?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl text-xs sm:text-sm',
                    confirmButton: 'rounded-xl px-4 py-2 font-semibold',
                    cancelButton: 'rounded-xl px-4 py-2 font-semibold text-slate-600'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(API_ROUTES.logout, {
                        method: 'POST',
                        credentials: 'include'
                    })
                    .then(() => {
                        localStorage.clear();
                        window.location.href = "/login";
                    })
                    .catch(() => {
                        localStorage.clear();
                        window.location.href = "/login";
                    });
                }
            });
        }

        // Ambil data non-sensitif dari localStorage untuk menghias sidebar
        document.addEventListener("DOMContentLoaded", function() {
            const namaSekolah = localStorage.getItem('nama_sekolah') || 'Sekolah E-Tabungan';
            const textElement = document.getElementById('txt-sidebar-sekolah');
            if (textElement) {
                textElement.innerText = namaSekolah;
            }
        });
    </script>
    @yield('scripts')
</body>
</html>