@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Kalkulator e-SKUM</h1>
    <p class="text-gray-500 mt-1">Estimasi Panjar Biaya Perkara Berdasarkan Radius Panggilan</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="kalkulator()">
    <!-- Form Kalkulator -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Perkara</label>
                    <select x-model="jenisPerkara" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <option value="gugatan">Gugatan (PN)</option>
                        <option value="permohonan">Permohonan (PN)</option>
                        <option value="bantahan">Bantahan</option>
                        <option value="banding">Banding</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Penggugat / Pemohon</label>
                        <input type="number" min="1" x-model.number="jumlahPenggugat" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Tergugat / Termohon</label>
                        <input type="number" min="0" x-model.number="jumlahTergugat" :disabled="jenisPerkara === 'permohonan'" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Radius Jarak Panggilan (Tergugat/Termohon)</label>
                    <select x-model.number="radius" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <option value="150000">Radius I (Jarak Dekat) - Rp 150.000 / Panggilan</option>
                        <option value="200000">Radius II (Jarak Sedang) - Rp 200.000 / Panggilan</option>
                        <option value="250000">Radius III (Jarak Jauh) - Rp 250.000 / Panggilan</option>
                        <option value="350000">Radius IV (Sangat Jauh / Beda Pulau) - Rp 350.000 / Panggilan</option>
                    </select>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
                <button @click="hitung()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors shadow-sm shadow-emerald-200">
                    Kalkulasi Biaya
                </button>
            </div>
        </div>
    </div>

    <!-- Hasil Kalkulasi -->
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-emerald-900 to-emerald-800 rounded-xl shadow-lg border border-emerald-700 text-white overflow-hidden relative">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.64-2.1 1.64-1.64 0-2.1-.92-2.16-1.66H8.21c.06 1.48 1.14 2.62 2.69 2.99V19h2.32v-1.67c1.68-.31 2.85-1.41 2.85-2.97 0-2.17-1.84-2.73-3.76-3.22z"></path></svg>
            </div>
            
            <div class="p-6 relative z-10">
                <h3 class="text-emerald-100 font-medium mb-1 text-sm uppercase tracking-wider">Estimasi Total Biaya</h3>
                <div class="text-4xl font-bold mb-6" x-text="formatRupiah(totalBiaya)">Rp 0</div>
                
                <div class="space-y-3 text-sm border-t border-emerald-700/50 pt-4">
                    <div class="flex justify-between">
                        <span class="text-emerald-100">Biaya Pendaftaran (PNBP)</span>
                        <span class="font-medium" x-text="formatRupiah(biayaDaftar)">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-emerald-100">Biaya Proses (ATK)</span>
                        <span class="font-medium" x-text="formatRupiah(biayaProses)">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-emerald-100">Redaksi & Materai</span>
                        <span class="font-medium" x-text="formatRupiah(biayaRedaksi)">Rp 0</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-emerald-700/50">
                        <span class="text-emerald-100">Biaya Panggilan (x<span x-text="jumlahPanggilan"></span>)</span>
                        <span class="font-medium text-emerald-300" x-text="formatRupiah(totalPanggilan)">Rp 0</span>
                    </div>
                </div>
                
                <div class="mt-6 bg-emerald-950/50 p-3 rounded-lg text-xs text-emerald-200/80 leading-relaxed">
                    * Perhitungan ini hanya estimasi panjar awal. Biaya riil dapat berubah menyesuaikan dinamika jalannya persidangan dan relaas panggilan.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kalkulator', () => ({
            jenisPerkara: 'gugatan',
            jumlahPenggugat: 1,
            jumlahTergugat: 1,
            radius: 150000,
            
            // Results
            biayaDaftar: 0,
            biayaProses: 0,
            biayaRedaksi: 0,
            jumlahPanggilan: 0,
            totalPanggilan: 0,
            totalBiaya: 0,
            
            init() {
                this.$watch('jenisPerkara', value => {
                    if(value === 'permohonan') {
                        this.jumlahTergugat = 0;
                    }
                });
                this.hitung();
            },
            
            hitung() {
                // Fixed costs
                this.biayaDaftar = 30000; // PNBP
                this.biayaProses = 75000; // ATK
                this.biayaRedaksi = 10000; // Redaksi + Materai
                
                // Variable costs (Panggilan)
                // Asumsi: Gugatan dipanggil 3x (Penggugat 2x, Tergugat 3x minimum)
                let faktorPanggilan = this.jenisPerkara === 'gugatan' ? 5 : 2; 
                
                let pihakTerpanggil = this.jumlahPenggugat + this.jumlahTergugat;
                if(this.jenisPerkara === 'permohonan') {
                    pihakTerpanggil = this.jumlahPenggugat;
                }
                
                this.jumlahPanggilan = pihakTerpanggil * faktorPanggilan;
                this.totalPanggilan = this.jumlahPanggilan * this.radius;
                
                this.totalBiaya = this.biayaDaftar + this.biayaProses + this.biayaRedaksi + this.totalPanggilan;
            },
            
            formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
            }
        }));
    });
</script>
<!-- Fallback AlpineJS if not globally loaded -->

@endsection
