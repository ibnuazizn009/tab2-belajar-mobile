<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') - E-Tabungan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (!localStorage.getItem('token_jwt')) { window.location.href = "/login"; }
    </script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between hidden md:flex">
            <div>
                <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                    <div class="bg-blue-600 p-2 rounded-xl text-white">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h1 class="text-white font-bold text-sm tracking-wide">E-TABUNGAN</h1>
                </div>
                
                <nav class="p-4 space-y-1">
                    <a href="/dashboard-admin" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl {{ request()->is('dashboard-admin') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}"><i class="fa-solid fa-chart-pie w-5"></i> Ringkasan</a>
                    <a href="/guru" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl {{ request()->is('guru') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}"><i class="fa-solid fa-chalkboard-user w-5"></i> Akun Guru</a>
                    <a href="/transaksi" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl {{ request()->is('transaksi') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}"><i class="fa-solid fa-receipt w-5"></i> Transaksi</a>
                    <a href="/pengaturan" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl {{ request()->is('pengaturan') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}"><i class="fa-solid fa-gear w-5"></i> Pengaturan</a>
                </nav>
            </div>

            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center justify-between">                    
                    <div class="flex items-center gap-3">
                        <img id="sidebar-avatar" src="https://ui-avatars.com/api/?background=3b82f6&color=fff&name=Admin" class="w-9 h-9 rounded-full border border-slate-700">
                        <div class="min-w-0">
                            <p id="txt-sidebar-nama" class="text-xs font-bold text-white truncate max-w-[80px]">Admin</p>
                            <p id="txt-sidebar-sekolah" class="text-[9px] text-slate-500 truncate max-w-[80px]">Memuat...</p>
                        </div>
                    </div>                    
                    <button onclick="logout()" class="flex items-center gap-1.5 text-slate-500 hover:text-red-400 transition duration-150 text-[14px] font-bold">
                        <i class="fa-solid fa-right-from-bracket text-[10px]"></i>
                        Keluar
                    </button>
                    
                </div>
            </div>
        </aside>
        <main class="flex-grow p-8">@yield('content')</main>
    </div>
    <script>
        function logout() { localStorage.clear(); window.location.href = "/login"; }
    </script>
</body>
</html>