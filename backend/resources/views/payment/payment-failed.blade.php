<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Gagal - E-Tabungan</title>

    <link rel="icon" type="image/x-icon" href="/favicon_ico.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center border border-slate-100">
        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-circle-xmark text-red-500 text-5xl animate-bounce"></i>
        </div>

        <h1 class="text-2xl font-bold text-slate-800 mb-2">Pembayaran Gagal / Dibatalkan</h1>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
            Mohon maaf, transaksi pembayaran untuk aktivasi paket layanan sekolah Anda tidak dapat diproses atau telah dibatalkan.
        </p>

        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-left text-xs text-slate-600 space-y-2 mb-6">
            <p class="font-semibold text-slate-700 text-center mb-1"><i class="fa-solid fa-circle-info mr-1 text-slate-500"></i> Kemungkinan Penyebab:</p>
            <p>• Sesi pembayaran di halaman Midtrans telah kedaluwarsa.</p>
            <p>• Anda menutup halaman pembayaran sebelum transaksi selesai.</p>
            <p>• Terjadi kesalahan teknis pada metode pembayaran yang dipilih.</p>
        </div>

        <div class="space-y-3">
            <a href="{{ url('/register') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-3 px-4 rounded-xl shadow-md shadow-blue-200 transition duration-200">
                <i class="fa-solid fa-arrow-rotate-left mr-2"></i>Coba Daftar & Bayar Lagi
            </a>
            <a href="https://wa.me/6285703817090" target="_blank" class="block w-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-sm py-3 px-4 rounded-xl transition duration-200">
                <i class="fa-brands fa-whatsapp mr-2 text-emerald-500"></i>Hubungi Bantuan CS
            </a>
        </div>

        <p class="text-[10px] text-slate-400 mt-8">© {{ date('Y') }} E-Tabungan Sekolah. All rights reserved.</p>
    </div>

</body>
</html>