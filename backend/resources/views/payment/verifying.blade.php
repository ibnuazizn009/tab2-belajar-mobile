<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memverifikasi Pembayaran - E-Tabungan Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-tr from-slate-50 via-gray-50 to-blue-50 font-sans antialiased min-h-screen flex flex-col justify-center items-center px-4 py-8">

    <div class="w-full max-w-md bg-white border border-slate-200 p-8 rounded-2xl shadow-xl shadow-slate-200/50 text-center">

        <!-- State: Loading (default) -->
        <div id="state-loading">
            <div class="inline-flex bg-blue-100 p-4 rounded-full text-blue-600 mb-4">
                <i class="fa-solid fa-spinner fa-spin text-3xl"></i>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Memverifikasi Pembayaran...</h2>
            <p class="text-sm text-slate-500 mt-2">Mohon tunggu sebentar, kami sedang mengonfirmasi status pembayaran Anda. Halaman ini akan otomatis memperbarui.</p>
            <p class="text-[11px] text-slate-400 mt-4">Order ID: {{ $orderId }}</p>
        </div>

        <!-- State: Sukses (hidden by default) -->
        <div id="state-success" class="hidden">
            <div class="inline-flex bg-emerald-100 p-4 rounded-full text-emerald-600 mb-4">
                <i class="fa-solid fa-circle-check text-3xl"></i>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Pembayaran Berhasil!</h2>
            <p class="text-sm text-slate-500 mt-2">Akun sekolah Anda sudah aktif. Mengalihkan Anda ke halaman login...</p>
        </div>

        <!-- State: Gagal (hidden by default) -->
        <div id="state-failed" class="hidden">
            <div class="inline-flex bg-rose-100 p-4 rounded-full text-rose-600 mb-4">
                <i class="fa-solid fa-circle-xmark text-3xl"></i>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Pembayaran Belum Berhasil</h2>
            <p class="text-sm text-slate-500 mt-2">Transaksi gagal, dibatalkan, atau kedaluwarsa. Silakan login kembali untuk mencoba pembayaran ulang.</p>
            <a href="/login" class="inline-block mt-4 text-xs font-semibold text-blue-600 hover:underline">Kembali ke Login</a>
        </div>

        <!-- State: Timeout (hidden by default) -->
        <div id="state-timeout" class="hidden">
            <div class="inline-flex bg-amber-100 p-4 rounded-full text-amber-600 mb-4">
                <i class="fa-solid fa-clock text-3xl"></i>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Masih Memproses</h2>
            <p class="text-sm text-slate-500 mt-2">Konfirmasi pembayaran membutuhkan waktu lebih lama dari biasanya. Anda bisa cek status nanti dengan login kembali.</p>
            <a href="/login" class="inline-block mt-4 text-xs font-semibold text-blue-600 hover:underline">Kembali ke Login</a>
        </div>

    </div>

    <script id="order-data" type="application/json">{!! json_encode($orderId) !!}</script>
    <script>
        const orderId = JSON.parse(document.getElementById('order-data').textContent);
        const MAX_ATTEMPTS = 20;       // total percobaan
        const POLL_INTERVAL_MS = 3000; // setiap 3 detik -> total ~60 detik

        let attempts = 0;

        function showState(stateId) {
            ['state-loading', 'state-success', 'state-failed', 'state-timeout'].forEach(id => {
                document.getElementById(id).classList.toggle('hidden', id !== stateId);
            });
        }

        async function checkStatus() {
            attempts++;

            try {
                const response = await fetch(`/api/services/tab2one/payment/status?order_id=${encodeURIComponent(orderId)}`);
                const data = await response.json();

                if (data.status === 'success') {
                    if (data.status_pembayaran === 'SUKSES') {
                        showState('state-success');
                        setTimeout(() => { window.location.href = '/login'; }, 2000);
                        return; // stop polling
                    }

                    if (data.status_pembayaran === 'GAGAL') {
                        showState('state-failed');
                        return; // stop polling
                    }
                    // kalau masih PENDING, lanjut polling di bawah
                }
            } catch (error) {
                console.error('Gagal cek status:', error);
                // tetap lanjut polling walau ada error sesaat (misal jaringan)
            }

            if (attempts >= MAX_ATTEMPTS) {
                showState('state-timeout');
                return;
            }

            setTimeout(checkStatus, POLL_INTERVAL_MS);
        }

        // Mulai polling begitu halaman dimuat
        checkStatus();
    </script>
</body>
</html>