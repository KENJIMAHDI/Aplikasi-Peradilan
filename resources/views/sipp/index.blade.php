@extends('layouts.app')
@section('title', 'SIPP - Penelusuran Perkara')
@section('page_title', 'SIPP - Sistem Informasi Penelusuran Perkara')

@section('content')

{{-- Search Bar --}}
<div class="bg-white rounded-lg shadow-sm p-6 mb-8">
    <form action="{{ route('sipp.index') }}" method="GET">
@csrf

                    
        <div class="flex gap-3">
            <input
                type="text"
                name="q"
                value="{{ $query ?? '' }}"
                placeholder="Cari berdasarkan Nomor Perkara atau Nama Hakim..."
                class="flex-1 shadow border rounded w-full py-3 px-4 text-gray-700 text-base leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400"
                id="search-input"
            >
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded transition duration-150">
                🔍 Telusuri
            </button>
        </div>
    </form>
</div>

{{-- Results --}}
@if(isset($query) && $query)
    <div class="mb-4 text-gray-600 text-sm">
        Ditemukan <strong>{{ $jadwals->count() }}</strong> hasil untuk kata kunci: <em>"{{ $query }}"</em>
    </div>

    @forelse($jadwals as $jadwal)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            {{-- Header Perkara --}}
            <div class="bg-blue-700 text-white px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Perkara No. {{ $jadwal->nomor_perkara }}</h3>
                        <p class="text-blue-200 text-sm mt-1">Hakim: {{ $jadwal->hakim->nama }} | Ruang: {{ $jadwal->ruangSidang->nama_ruangan }}</p>
                    </div>
                    @php
                        $latestStatus = $jadwal->riwayatPerkaras->last()?->status_perkara ?? 'Proses';
                    @endphp
                    <span class="
                        px-4 py-2 rounded-full text-sm font-bold
                        {{ $latestStatus == 'Putus' ? 'bg-green-400 text-green-900' : '' }}
                        {{ $latestStatus == 'Banding' ? 'bg-purple-400 text-purple-900' : '' }}
                        {{ $latestStatus == 'Proses' ? 'bg-yellow-300 text-yellow-900' : '' }}
                    ">
                        {{ $latestStatus }}
                    </span>
                </div>
            </div>

            {{-- Detail Sidang --}}
            <div class="px-6 py-4 border-b grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                <div>
                    <p class="text-xs uppercase text-gray-400 font-semibold">Waktu Mulai</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-400 font-semibold">Waktu Selesai</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-400 font-semibold">Status Sidang</p>
                    <p class="font-medium">{{ $jadwal->status }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-400 font-semibold">Total Riwayat</p>
                    <p class="font-medium">{{ $jadwal->riwayatPerkaras->count() }} Agenda</p>
                </div>
            </div>

            {{-- Timeline Riwayat --}}
            <div class="px-6 py-5">
                <h4 class="font-semibold text-gray-700 mb-4">Timeline Riwayat Perkara</h4>
                @if($jadwal->riwayatPerkaras->count() > 0)
                    <div class="relative">
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-blue-200"></div>
                        @foreach($jadwal->riwayatPerkaras as $riwayat)
                            <div class="relative pl-12 pb-6">
                                <div class="absolute left-0 top-1 w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold
                                    {{ $riwayat->status_perkara == 'Putus' ? 'bg-green-500' : '' }}
                                    {{ $riwayat->status_perkara == 'Banding' ? 'bg-purple-500' : '' }}
                                    {{ $riwayat->status_perkara == 'Proses' ? 'bg-blue-500' : '' }}
                                ">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-1">
                                        <p class="font-semibold text-gray-800">{{ $riwayat->agenda }}</p>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($riwayat->tanggal_sidang)->format('d M Y') }}</span>
                                    </div>
                                    @if($riwayat->hasil_sidang)
                                        <p class="text-sm text-gray-600 mt-1">{{ $riwayat->hasil_sidang }}</p>
                                    @endif
                                    @if($riwayat->amar_putusan)
                                        <p class="text-sm font-medium text-blue-700 mt-2">⚖️ Amar: {{ $riwayat->amar_putusan }}</p>
                                    @endif
                                    <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded-full
                                        {{ $riwayat->status_perkara == 'Putus' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $riwayat->status_perkara == 'Banding' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $riwayat->status_perkara == 'Proses' ? 'bg-blue-100 text-blue-700' : '' }}
                                    ">
                                        {{ $riwayat->status_perkara }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm italic">Belum ada riwayat sidang yang dicatat untuk perkara ini.</p>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="text-5xl mb-4">🔍</div>
            <h3 class="text-lg font-semibold text-gray-700">Perkara Tidak Ditemukan</h3>
            <p class="text-gray-400 text-sm mt-2">Tidak ada perkara yang cocok dengan kata kunci <em>"{{ $query }}"</em>.</p>
        </div>
    @endforelse
@else
    <div class="bg-white rounded-lg shadow-sm p-12 text-center text-gray-400">
        <div class="text-5xl mb-4">⚖️</div>
        <h3 class="text-lg font-semibold text-gray-700">Penelusuran Perkara Publik</h3>
        <p class="text-sm mt-2">Masukkan nomor perkara atau nama hakim untuk memulai penelusuran.</p>
    </div>
@endif

@endsection
