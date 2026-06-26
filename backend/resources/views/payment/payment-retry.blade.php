<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Tertunda - E-Tabungan Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-tr from-slate-50 via-gray-50 to-blue-50 font-sans antialiased min-h-screen flex flex-col justify-center items-center px-4 py-8">

    <div class="w-full max-w-md bg-white border border-slate-200 p-8 rounded-2xl shadow-xl shadow-slate-200/50 relative">
        
        <div class="text-center mb-6">
            <div class="inline-flex bg-amber-100 p-4 rounded-full text-amber-600 mb-4 animate-bounce">
                <i class="fa-solid fa-clock-rotate-left text-3xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pembayaran Belum Selesai</h2>
            <p class="text-sm text-slate-500 mt-2">Pendaftaran Anda tersimpan, namun kami belum menerima pembayaran untuk aktivasi sistem.</p>
        </div>

        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-3 mb-6 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-500">Nama Sekolah:</span>
                <span class="font-bold text-slate-800">{{ $sekolah->nama_sekolah }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Paket Dipilih:</span>
                <span class="font-bold px-2.5 py-0.5 rounded-full text-xs {{ $sekolah->paket_layanan === 'GOLDEN' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-800' }}">
                    Paket {{ $sekolah->paket_layanan }}
                </span>
            </div>
            <div class="flex justify-between border-t border-slate-200/60 pt-3">
                <span class="text-slate-500 font-semibold">Total Tagihan:</span>
                <span class="font-extrabold text-blue-600 text-base">
                    Rp {{ number_format($sekolah->paket_layanan === 'SILVER' ? 150000 : 350000, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="space-y-3">
            <button id="btn-pay" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200 shadow-lg shadow-blue-600/20 tracking-wide flex items-center justify-center gap-2 transform active:scale-[0.98]">
                <span id="btn-text">Lanjutkan Pembayaran Sekarang</span>
                <i id="btn-icon" class="fa-solid fa-credit-card text-xs"></i>
            </button>

            <a href="/login" class="w-full py-3 text-center block text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Halaman Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const btnPay = document.getElementById('btn-pay');
        const btnText = document.getElementById('btn-text');
        const btnIcon = document.getElementById('btn-icon');

        btnPay.addEventListener('click', function() {
            btnPay.disabled = true;
            btnText.innerText = 'Menyiapkan Gerbang Pembayaran...';
            btnIcon.className = 'fa-solid fa-spinner fa-spin';
        
            fetch('/payment/retry', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ token: '{{ $token }}' }) // 🌟 baris baru
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Mengalihkan Anda ke Midtrans...',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'max-w-[400px] w-[90%] text-sm' }
                    });
        
                    setTimeout(() => { window.location.href = data.redirect_url; }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal memproses tautan baru.',
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#ef4444',
                        customClass: { popup: 'max-w-[400px] w-[90%] text-sm' }
                    });
                    resetBtn();
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Gagal menghubungkan ke server pembayar.',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#ef4444',
                    customClass: { popup: 'max-w-[400px] w-[90%] text-sm' }
                });
                resetBtn();
            });
        });

        function resetBtn() {
            btnPay.disabled = false;
            btnText.innerText = 'Lanjutkan Pembayaran Sekarang';
            btnIcon.className = 'fa-solid fa-credit-card text-xs';
        }
    </script>
</body>
</html>