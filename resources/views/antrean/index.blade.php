@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Presensi & Antrean Sidang</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar kehadiran pihak berperkara dan antrean sidang hari ini secara realtime.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('antrean.public') }}" target="_blank" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Buka Kiosk Lobi (Public)
        </a>
    </div>
</div>

<!-- Ringkasan Statistik -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Jadwal Hari Ini</p>
        <p class="text-3xl font-black text-gray-900 mt-2">{{ $jadwals->count() }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Siap Sidang</p>
        <p class="text-3xl font-black text-emerald-600 mt-2">{{ $jadwals->where('status_kelengkapan', 'siap_sidang')->count() }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Penggugat Hadir</p>
        <p class="text-3xl font-black text-blue-600 mt-2">{{ $jadwals->where('status_penggugat', 'hadir')->count() }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tergugat Hadir</p>
        <p class="text-3xl font-black text-indigo-600 mt-2">{{ $jadwals->where('status_tergugat', 'hadir')->count() }}</p>
    </div>
</div>

<!-- Main Table Container -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-55 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600">Nomor Perkara</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Jam Sidang</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Ruangan & Hakim</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Kehadiran Penggugat</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Kehadiran Tergugat</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Status Kelengkapan</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($jadwals as $jadwal)
                <tr class="hover:bg-emerald-50/10 transition-colors duration-150">
                    <td class="px-6 py-4 font-medium text-emerald-700">{{ $jadwal->nomor_perkara }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-900 font-medium">{{ $jadwal->ruangSidang->nama_ruangan ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $jadwal->hakim->nama ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($jadwal->status_penggugat === 'Hadir & Siap Sidang' || $jadwal->status_penggugat === 'hadir')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">Hadir & Siap Sidang</span>
                        @elseif(str_contains(strtolower($jadwal->status_penggugat), 'izin'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">{{ $jadwal->status_penggugat }}</span>
                        @elseif(str_contains(strtolower($jadwal->status_penggugat), 'sakit'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200">{{ $jadwal->status_penggugat }}</span>
                        @elseif($jadwal->status_penggugat === 'belum_hadir' || empty($jadwal->status_penggugat))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">Belum Hadir</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">{{ $jadwal->status_penggugat }}</span>
                        @endif
                        @if($jadwal->no_hp_penggugat)
                            <div class="text-xs text-gray-400 mt-1 font-mono">{{ $jadwal->no_hp_penggugat }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($jadwal->status_tergugat === 'Hadir & Siap Sidang' || $jadwal->status_tergugat === 'hadir')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">Hadir & Siap Sidang</span>
                        @elseif(str_contains(strtolower($jadwal->status_tergugat), 'izin'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">{{ $jadwal->status_tergugat }}</span>
                        @elseif(str_contains(strtolower($jadwal->status_tergugat), 'sakit'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200">{{ $jadwal->status_tergugat }}</span>
                        @elseif($jadwal->status_tergugat === 'belum_hadir' || empty($jadwal->status_tergugat))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">Belum Hadir</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">{{ $jadwal->status_tergugat }}</span>
                        @endif
                        @if($jadwal->no_hp_tergugat)
                            <div class="text-xs text-gray-400 mt-1 font-mono">{{ $jadwal->no_hp_tergugat }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($jadwal->status_kelengkapan === 'siap_sidang')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm shadow-emerald-100">
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                Siap Sidang
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                Belum Lengkap
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($jadwal->no_hp_penggugat || $jadwal->no_hp_tergugat)
                            <form action="{{ route('jadwal.panggil', $jadwal->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm shadow-emerald-100 inline-flex items-center gap-1.5 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    Panggil WA
                                </button>
                            </form>
                        @else
                            <span class="text-gray-400 text-xs italic">Menunggu Check-In</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">Tidak ada jadwal sidang hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
