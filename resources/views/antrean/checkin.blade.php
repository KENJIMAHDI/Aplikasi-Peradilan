<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In Mandiri Sidang - APLIKASI PERADILAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col justify-between font-sans">

    <header class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-2">
            <div class="bg-emerald-600 text-white p-2 rounded-lg font-bold">⚖️</div>
            <span class="font-bold text-gray-800 text-lg tracking-wide">APLIKASI PERADILAN</span>
        </div>
        <a href="/antrean-sidang" class="text-emerald-700 font-semibold hover:underline text-sm flex items-center gap-1">
            Masuk Dashboard &rarr;
        </a>
    </header>

    <main class="flex-grow flex items-center justify-center p-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-xl w-full max-w-xl p-6 sm:p-8">
            
            <div class="text-center mb-6">
                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    Lobi Peradilan
                </span>
                <h1 class="text-2xl font-bold text-gray-800 mt-2">Check-In Mandiri Sidang</h1>
                <p class="text-xs text-gray-500 mt-1">Konfirmasi kehadiran pihak berperkara hari ini.</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-semibold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('antrean.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Nomor Perkara</label>
                    <input type="text" name="no_perkara" required placeholder="Contoh: 123/Pdt.G/2026/PN.Smg" 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Peran Anda</label>
                    <select name="peran" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm outline-none bg-white">
                        <option value="penggugat">Pihak Penggugat / Pemohon</option>
                        <option value="tergugat">Pihak Tergugat / Termohon</option>
                        <option value="kuasa_hukum">Kuasa Hukum / Pengacara</option>
                    </select>
                </div>

                <!-- DROPDOWN 6 STATUS KEHADIRAN -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Status Kehadiran</label>
                        <select name="status_kehadiran" id="status_kehadiran" onchange="toggleCatatan()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm outline-none bg-white font-medium text-gray-800">
                            <option value="hadir">Hadir</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="tidak_dapat_hadir">Tidak Dapat Hadir (Catatan Khusus)</option>
                            <option value="agenda_kesibukan">Agenda Kesibukan (Catatan Khusus)</option>
                            <option value="luar_kota">Di Luar Kota / Provinsi</option>
                        </select>
                    </div>

                    <!-- TEXTAREA CATATAN KHUSUS (MUNCUL PADA OPSI 4 & 5) -->
                    <div id="catatan_wrapper" class="space-y-1 hidden">
                        <label class="block text-xs font-bold text-amber-700 uppercase tracking-wide">Catatan Khusus / Alasan</label>
                        <textarea name="catatan_khusus" rows="2" placeholder="Tuliskan detail alasan / agenda kesibukan Anda..." 
                                  class="w-full px-3 py-2 rounded-lg border border-amber-300 focus:ring-2 focus:ring-amber-500 text-sm outline-none bg-amber-50"></textarea>
                    </div>
                </div>

                <script>
                    function toggleCatatan() {
                        var select = document.getElementById('status_kehadiran');
                        var wrapper = document.getElementById('catatan_wrapper');
                        if (select.value === 'tidak_dapat_hadir' || select.value === 'agenda_kesibukan') {
                            wrapper.classList.remove('hidden');
                        } else {
                            wrapper.classList.add('hidden');
                        }
                    }
                </script>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Nomor WhatsApp Aktif</label>
                    <input type="text" name="no_whatsapp" required placeholder="Contoh: 081234567890" 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm outline-none">
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow transition-all text-sm">
                    Kirim Konfirmasi Kehadiran
                </button>
            </form>

        </div>
    </main>

    <footer class="text-center py-4 text-xs text-gray-400">
        &copy; 2026 Aplikasi Peradilan. All rights reserved.
    </footer>

</body>
</html>
