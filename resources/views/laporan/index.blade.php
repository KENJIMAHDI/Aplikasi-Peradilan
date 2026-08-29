@extends('layouts.app')

@section('content')
<div class="print:m-0">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan & Statistik Perkara</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan analitik performa penanganan dan penyelesaian perkara pengadilan secara realtime.</p>
        </div>
        <button onclick="window.print()" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg font-medium shadow-sm flex items-center gap-2 transition-colors print:hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak / Export PDF
        </button>
    </div>

    <!-- Ringkasan Statistik Utama -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-gray-400">Total Perkara Masuk</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-black text-gray-900 mt-4">{{ $totalMasuk }}</p>
            <p class="text-xs text-gray-500 mt-2">Seluruh Perdata & Pidana</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-emerald-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-emerald-600">Perkara Diputus/Selesai</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-black text-emerald-700 mt-4">{{ $totalPutus }}</p>
            <p class="text-xs text-emerald-600 mt-2 font-medium">Telah Berkekuatan Hukum</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-amber-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-amber-600">Sisa Perkara Aktif</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-black text-amber-700 mt-4">{{ $totalSisa }}</p>
            <p class="text-xs text-amber-600 mt-2 font-medium">Dalam Tahap Persidangan</p>
        </div>

        <div class="bg-emerald-600 p-6 rounded-xl shadow-sm border border-emerald-500 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-white/10 rounded-bl-full z-0"></div>
            <div class="flex items-center justify-between relative z-10">
                <span class="text-xs font-bold uppercase text-emerald-100">Clearance Rate</span>
                <span class="p-2 bg-white/20 text-white rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
            </div>
            <p class="text-4xl font-black mt-4 relative z-10">{{ $clearanceRate }}%</p>
            <p class="text-xs text-emerald-100 mt-2 relative z-10 font-medium">Rasio Penyelesaian Perkara</p>
        </div>
    </div>

    <!-- Tabel Distribusi Perkara Aktif -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-bold text-gray-900">Distribusi Beban Penanganan Perkara</h2>
            <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2.5 py-1 rounded-full">Data Realtime</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="p-4 font-semibold">Klasifikasi Perkara</th>
                        <th class="p-4 font-semibold text-center">Sisa Awal</th>
                        <th class="p-4 font-semibold text-center">Masuk / Daftar</th>
                        <th class="p-4 font-semibold text-center">Putus / Selesai</th>
                        <th class="p-4 font-semibold text-right">Sisa Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($distribusi as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 font-bold text-gray-900">{{ $item['kategori'] }}</td>
                        <td class="p-4 text-center text-gray-600">{{ $item['sisa_lalu'] }}</td>
                        <td class="p-4 text-center font-semibold text-blue-600">{{ $item['masuk'] }}</td>
                        <td class="p-4 text-center font-semibold text-emerald-600">{{ $item['putus'] }}</td>
                        <td class="p-4 text-right font-black text-gray-900">{{ $item['sisa'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100/75 font-bold text-gray-900 border-t-2 border-gray-200">
                        <td class="p-4">TOTAL KESELURUHAN</td>
                        <td class="p-4 text-center">{{ array_sum(array_column($distribusi, 'sisa_lalu')) }}</td>
                        <td class="p-4 text-center text-blue-700">{{ $totalMasuk }}</td>
                        <td class="p-4 text-center text-emerald-700">{{ $totalPutus }}</td>
                        <td class="p-4 text-right text-emerald-800 text-base font-black">{{ $totalSisa }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Beban Sidang Per Hakim -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-bold text-gray-900">Rekapitulasi Persidangan Majelis Hakim</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="p-4 font-semibold">Nama Hakim</th>
                        <th class="p-4 font-semibold">NIP</th>
                        <th class="p-4 font-semibold">Jabatan</th>
                        <th class="p-4 font-semibold text-right">Total Jadwal Sidang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($hakims as $h)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 font-bold text-gray-900">{{ $h->nama }}</td>
                        <td class="p-4 text-gray-600">{{ $h->nip ?? '-' }}</td>
                        <td class="p-4 text-gray-600">{{ $h->jabatan ?? 'Hakim Anggota' }}</td>
                        <td class="p-4 text-right font-black text-emerald-700 text-base">
                            {{ $h->jadwal_sidangs_count }} Sidang
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">Belum ada data hakim terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
