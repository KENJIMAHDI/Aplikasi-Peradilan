@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false, editData: {}, searchQuery: '', filterStatus: '' }">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Monitoring Relaas Panggilan</h1>
            <p class="text-sm text-gray-500 mt-1">Lacak status panggilan sidang dan penyampaian relaas oleh Jurusita secara realtime.</p>
        </div>
    </div>

    <!-- Card Statistik Realtime -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <h3 class="text-gray-500 text-sm font-medium mb-1">Total Panggilan Sidang</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $totalPanggilan }}</p>
            </div>
            <p class="text-xs text-gray-400 mt-3">Akumulasi seluruh perkara terdaftar</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-emerald-100 p-6 relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="absolute right-0 top-0 w-16 h-16 bg-emerald-50 rounded-bl-full z-0"></div>
            <div>
                <h3 class="text-emerald-700 text-sm font-medium mb-1 relative z-10">Panggilan Sukses (Siap/Patut)</h3>
                <p class="text-3xl font-bold text-emerald-900 relative z-10">{{ $panggilanPatut }}</p>
            </div>
            <p class="text-xs text-emerald-600 mt-3 relative z-10 font-medium">Relaas telah selesai disampaikan</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 p-6 relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="absolute right-0 top-0 w-16 h-16 bg-amber-50 rounded-bl-full z-0"></div>
            <div>
                <h3 class="text-amber-700 text-sm font-medium mb-1 relative z-10">Belum Dipanggil / Proses</h3>
                <p class="text-3xl font-bold text-amber-900 relative z-10">{{ $panggilanBelum }}</p>
            </div>
            <p class="text-xs text-amber-600 mt-3 relative z-10 font-medium">Menunggu penyampaian oleh Jurusita</p>
        </div>
    </div>

    <!-- Daftar Relaas Realtime Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-wrap gap-4 justify-between items-center">
            <div class="flex flex-wrap gap-3 items-center flex-grow">
                <div class="relative w-72">
                    <input type="text" x-model="searchQuery" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 bg-white" placeholder="Cari Nomor Perkara / Hakim...">
                </div>
                <div class="w-56">
                    <select x-model="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        <option value="">Semua Status Relaas</option>
                        <option value="Relaas Siap/Patut">Relaas Siap/Patut</option>
                        <option value="Belum Dipanggil">Belum Dipanggil</option>
                        <option value="Dalam Perjalanan">Dalam Perjalanan</option>
                        <option value="Tidak Bertemu">Tidak Bertemu</option>
                    </select>
                </div>
            </div>
            <span class="text-xs text-gray-500 font-medium">Menampilkan data realtime dari Database SIPP</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="p-4 font-semibold">Nomor Perkara</th>
                        <th class="p-4 font-semibold">Jadwal & Waktu Sidang</th>
                        <th class="p-4 font-semibold">Majelis Hakim</th>
                        <th class="p-4 font-semibold">Ruang Sidang</th>
                        <th class="p-4 font-semibold">Status Relaas Panggilan</th>
                        <th class="p-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($daftarRelaas as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors" 
                        x-show="(searchQuery === '' || '{{ strtolower($item->nomor_perkara) }} {{ strtolower($item->hakim->nama ?? '') }}'.includes(searchQuery.toLowerCase())) && (filterStatus === '' || '{{ $item->status_relaas }}' === filterStatus)">
                        <td class="p-4 font-medium text-gray-900">
                            <span class="text-emerald-700 font-bold">{{ $item->nomor_perkara }}</span>
                        </td>
                        <td class="p-4 text-gray-600">
                            <div class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($item->waktu_mulai)->translatedFormat('d M Y') }}</div>
                            <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }} WIB</div>
                        </td>
                        <td class="p-4 text-gray-800">
                            {{ $item->hakim->nama ?? '-' }}
                        </td>
                        <td class="p-4 text-gray-600">
                            {{ $item->ruangSidang->nama_ruangan ?? '-' }}
                        </td>
                        <td class="p-4">
                            @if($item->status_relaas === 'Relaas Siap/Patut')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Relaas Siap/Patut
                                </span>
                            @elseif($item->status_relaas === 'Dalam Perjalanan')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    Dalam Perjalanan
                                </span>
                            @elseif($item->status_relaas === 'Tidak Bertemu')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    Tidak Bertemu
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    Belum Dipanggil
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            @can('manage-perkara')
                            <button type="button" @click="showModal = true; editData = {{ json_encode($item) }}" class="text-emerald-600 hover:text-emerald-800 font-semibold text-xs py-1.5 px-3 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200">
                                Update Status
                            </button>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">Belum ada data jadwal sidang & relaas tercatat di database.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Update Status Relaas -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/50" 
         style="display: none;">
        
        <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl p-6 overflow-hidden my-auto border border-gray-100 text-left" @click.away="showModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-900">Perbarui Status Relaas</h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="'/relaas-panggilan/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase">Nomor Perkara</label>
                        <p class="mt-1 text-base font-bold text-gray-900" x-text="editData.nomor_perkara"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Relaas Penyampaian</label>
                        <select name="status_relaas" :value="editData.status_relaas" required class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            <option value="Relaas Siap/Patut">Relaas Siap/Patut</option>
                            <option value="Belum Dipanggil">Belum Dipanggil</option>
                            <option value="Dalam Perjalanan">Dalam Perjalanan</option>
                            <option value="Tidak Bertemu">Tidak Bertemu</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex flex-row-reverse gap-2 border-t border-gray-100 pt-4">
                    <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none">
                        Simpan Perubahan
                    </button>
                    <button type="button" @click="showModal = false" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
