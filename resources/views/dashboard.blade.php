@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Statistik</h1>
        <p class="text-gray-500 text-sm">Ringkasan data perkara dan jadwal sidang hari ini.</p>
    </div>
    @if(isset($q) && $q !== '')
    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
        Reset Pencarian
    </a>
    @endif
</div>

@if(isset($searchResults))
<!-- Search Results Section -->
<div class="mb-8 bg-white rounded-2xl shadow-sm border border-emerald-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-emerald-100 flex items-center justify-between bg-emerald-50/50">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <h2 class="font-semibold text-emerald-900">Hasil Pencarian untuk: <span class="font-bold">"{{ $q }}"</span></h2>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full">
            Ditemukan {{ count($searchResults) }} Perkara
        </span>
    </div>
    <div class="p-6">
        @if(count($searchResults) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 rounded-lg">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Jenis / Kategori</th>
                            <th class="px-4 py-3">No Perkara</th>
                            <th class="px-4 py-3">Tanggal Daftar</th>
                            <th class="px-4 py-3">Pihak 1 (Penggugat/Terdakwa)</th>
                            <th class="px-4 py-3">Pihak 2 (Tergugat/Jaksa)</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($searchResults as $result)
                        <tr class="border-b last:border-0 hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full border {{ $result['type'] === 'Perdata' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                    {{ $result['type'] }} ({{ $result['kategori'] }})
                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-emerald-700">{{ $result['nomor'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $result['tanggal'] }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $result['pihak1'] }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $result['pihak2'] }}</td>
                            <td class="px-4 py-3 text-right">
                                <span class="px-2.5 py-1 text-xs font-semibold border {{ $result['status'] === 'Diajukan' || $result['status'] === 'Proses' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : ($result['status'] === 'Sedang Di Proses' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }} rounded-full">
                                    {{ $result['status'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-gray-500 font-medium">Tidak ada perkara yang cocok dengan kata kunci tersebut.</p>
            </div>
        @endif
    </div>
</div>
@endif

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
        <p class="text-sm text-gray-500 font-medium">Sisa Lalu</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['sisa_lalu'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute right-0 top-0 w-16 h-16 bg-blue-500/10 rounded-bl-full -mr-2 -mt-2"></div>
        <p class="text-sm text-blue-600 font-medium">Masuk</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['masuk'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute right-0 top-0 w-16 h-16 bg-emerald-500/10 rounded-bl-full -mr-2 -mt-2"></div>
        <p class="text-sm text-emerald-600 font-medium">Putus</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['putus'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute right-0 top-0 w-16 h-16 bg-purple-500/10 rounded-bl-full -mr-2 -mt-2"></div>
        <p class="text-sm text-purple-600 font-medium">Minutasi</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['minutasi'] }}</p>
    </div>
    <div class="bg-emerald-600 p-5 rounded-2xl shadow-sm border border-emerald-500 flex flex-col justify-between text-white relative overflow-hidden">
        <svg class="absolute right-2 bottom-2 w-16 h-16 text-emerald-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        <p class="text-sm text-emerald-100 font-medium z-10">Sisa Perkara</p>
        <p class="text-3xl font-bold mt-2 z-10">{{ $stats['sisa'] }}</p>
    </div>
</div>

<!-- Jadwal Hari Ini -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <h2 class="font-semibold text-gray-800">Jadwal Sidang Hari Ini</h2>
        <a href="{{ route('jadwal.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Lihat Semua &rarr;</a>
    </div>
    <div class="p-6">
        @if(count($jadwalHariIni) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 rounded-lg">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">No Perkara</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Ruang Sidang</th>
                            <th class="px-4 py-3">Majelis Hakim</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalHariIni as $jadwal)
                        <tr class="border-b last:border-0 hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $jadwal->nomor_perkara }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 bg-gray-100 rounded-full font-medium">{{ $jadwal->waktu_mulai->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $jadwal->ruangSidang->nama_ruangan ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $jadwal->hakim->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <span class="px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full">
                                    {{ $jadwal->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-gray-500 font-medium">Tidak ada jadwal sidang hari ini.</p>
            </div>
        @endif
    </div>
</div>

<!-- Log Balasan WA Gateway (2-Arah) -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-8">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            <h2 class="font-semibold text-gray-800">Log Balasan WA Gateway (Notifikasi 2 Arah)</h2>
        </div>
        <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2.5 py-1 rounded-full">Realtime Webhook</span>
    </div>
    <div class="p-6" id="wa-replies-wrapper">
        @if(isset($waReplies) && count($waReplies) > 0)
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($waReplies as $idx => $reply)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex items-start space-x-3">
                                <div class="relative">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 font-bold">
                                        WA
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div>
                                        <div class="text-sm">
                                            <span class="font-bold text-gray-900">{{ $reply['sender'] }}</span>
                                        </div>
                                        <p class="mt-0.5 text-xs text-gray-400">{{ \Carbon\Carbon::parse($reply['timestamp'])->diffForHumans() }} ({{ $reply['timestamp'] }})</p>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-xl p-3 inline-block">
                                        <p>{{ $reply['message'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <p class="text-gray-500 font-medium">Belum ada log balasan chat masuk.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function fetchWaReplies() {
        fetch('{{ route('admin.wa_replies.json') }}')
            .then(response => response.json())
            .then(data => {
                const wrapper = document.getElementById('wa-replies-wrapper');
                if (data && data.length > 0) {
                    let html = '<div class="flow-root"><ul role="list" class="-mb-8">';
                    data.forEach((reply, idx) => {
                        const isLast = idx === data.length - 1;
                        html += `
                        <li>
                            <div class="relative pb-8">
                                ${!isLast ? '<span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>' : ''}
                                <div class="relative flex items-start space-x-3">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 font-bold">
                                            WA
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="text-sm">
                                                <span class="font-bold text-gray-900">${reply.sender}</span>
                                            </div>
                                            <p class="mt-0.5 text-xs text-gray-400">${reply.timestamp}</p>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-xl p-3 inline-block">
                                            <p>${reply.message}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>`;
                    });
                    html += '</ul></div>';
                    wrapper.innerHTML = html;
                } else {
                    wrapper.innerHTML = `
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada log balasan chat masuk.</p>
                        </div>`;
                }
            })
            .catch(err => console.error('Error fetching WA replies:', err));
    }
    
    // Poll every 5 seconds
    setInterval(fetchWaReplies, 5000);
</script>
@endpush
@endsection
