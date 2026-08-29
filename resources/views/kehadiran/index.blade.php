@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Kehadiran Hakim</h1>
        <p class="text-gray-500 mt-1">Daftar presensi dan status kehadiran hakim</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="{ showModal: false, showHakimModal: false, isEdit: false, editData: {}, searchQuery: '', searchDate: '' }">
     
    <!-- Header & Filter -->
    <div class="p-4 border-b border-gray-100 flex flex-wrap gap-4 justify-between items-end bg-gray-50/50">
        <div class="flex flex-wrap gap-4 items-end flex-grow">
            <div class="flex-1 min-w-[200px] max-w-xs">
                <input type="text" x-model="searchQuery" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Cari Nama Hakim...">
            </div>
            <div class="w-40">
                <input type="date" x-model="searchDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
        </div>
        <div class="flex gap-2">
            @can('manage-users')
            <button @click="showHakimModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium shadow flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Tambah Hakim
            </button>
            @endcan
            @can('manage-hakim')
            <button @click="showModal = true; isEdit = false; editData = { status: 'Hadir' }" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium shadow flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Kehadiran
            </button>
            @endcan
        </div>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-white border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600">Nama Hakim</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Tanggal</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Status</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($presensi as $item)
                <tr class="hover:bg-emerald-50/20 transition-colors duration-150"
                    x-show="(searchQuery === '' || '{{ strtolower($item->hakim ? $item->hakim->nama : '') }}'.includes(searchQuery.toLowerCase())) && 
                            (searchDate === '' || '{{ substr($item->tanggal, 0, 10) }}' === searchDate)">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">{{ $item->hakim ? substr($item->hakim->nama, 0, 1) : 'H' }}</div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $item->hakim ? $item->hakim->nama : 'Data tidak ditemukan' }}</div>
                                <div class="text-xs text-gray-400">NIP: {{ $item->hakim && $item->hakim->nip ? $item->hakim->nip : '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
                    <td class="px-6 py-4">
                        @if($item->status === 'Hadir')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border-green-200 border">Hadir</span>
                        @elseif($item->status === 'Sakit' || $item->status === 'Izin')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border-yellow-200 border">{{ $item->status }}</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border-red-200 border">{{ $item->status ?: 'Alpha' }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @can('manage-perkara')
                        <button type="button" @click="showModal = true; isEdit = true; editData = {{ json_encode($item) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold transition-colors">Edit</button>
                        <form action="/kehadiran/{{ $item->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold transition-colors">Hapus</button>
                        </form>
                        @else
                        <span class="text-gray-400 text-sm">-</span>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-50 mb-3 border border-gray-100">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <p>Belum ada data presensi.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Wrapper (Tepat di Tengah & Tidak Terpotong Layout) -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/50" 
         style="display: none;">
        
        <div class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl p-6 overflow-hidden my-auto border border-gray-100 max-h-[90vh] overflow-y-auto text-left" @click.away="showModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Ubah Status Kehadiran' : 'Catat Kehadiran Hakim'"></h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>

            <!-- Body Form CRUD -->
            <div>
                <form :action="isEdit ? '/kehadiran/' + editData.id : '{{ route('kehadiran.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Pilih Hakim</label>
                            <select name="hakim_id" x-model="editData.hakim_id" :disabled="isEdit" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" :class="isEdit ? 'bg-gray-100 text-gray-500' : ''">
                                <option value="" disabled>-- Pilih Hakim --</option>
                                @foreach($hakims as $h)
                                    <option value="{{ $h->id }}">{{ $h->nama }}</option>
                                @endforeach
                            </select>
                            <template x-if="isEdit">
                                <!-- Pass hakim_id to backend since disabled inputs are not submitted -->
                                <input type="hidden" name="hakim_id" :value="editData.hakim_id">
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Tanggal</label>
                            <input type="date" name="tanggal" :value="isEdit && editData.tanggal ? editData.tanggal.substring(0, 10) : '{{ date('Y-m-d') }}'" :disabled="isEdit" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" :class="isEdit ? 'bg-gray-100 text-gray-500' : ''">
                            <template x-if="isEdit">
                                <input type="hidden" name="tanggal" :value="editData.tanggal ? editData.tanggal.substring(0, 10) : ''">
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Status</label>
                            <select name="status" x-model="editData.status" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="Hadir">Hadir</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Cuti">Cuti</option>
                                <option value="Dinas Luar">Dinas Luar</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex flex-row-reverse border-t border-gray-100 pt-4 gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700 sm:w-auto">
                            Simpan Data
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Hakim -->
    <div x-show="showHakimModal" 
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/50" 
         style="display: none;">
        
        <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl p-6 overflow-hidden my-auto border border-gray-100 max-h-[90vh] overflow-y-auto text-left" @click.away="showHakimModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Tambah Hakim Baru</h3>
                <button type="button" @click="showHakimModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>

            <div>
                <form action="{{ route('hakim.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">NIP Hakim</label>
                            <input type="text" name="nip" placeholder="Masukkan NIP Hakim..." required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nama Lengkap Hakim</label>
                            <input type="text" name="nama" placeholder="Masukkan Nama Lengkap Hakim..." required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Chat ID Telegram (Opsional)</label>
                            <input type="text" name="chat_id_telegram" placeholder="Masukkan Chat ID Telegram..." class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex flex-row-reverse border-t border-gray-100 pt-4 gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-700 sm:w-auto">
                            Simpan Hakim
                        </button>
                        <button type="button" @click="showHakimModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>

                <!-- Daftar Hakim Terdaftar -->
                <div class="mt-6 border-t border-gray-100 pt-4">
                    <h4 class="text-sm font-bold text-gray-800 mb-3">Daftar Hakim Terdaftar</h4>
                    <div class="space-y-2 max-h-[200px] overflow-y-auto pr-1">
                        @foreach($hakims as $h)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div>
                                <p class="text-xs font-semibold text-gray-900">{{ $h->nama }}</p>
                                <p class="text-[10px] text-gray-500">NIP: {{ $h->nip }}</p>
                            </div>
                            <form action="/hakim/{{ $h->id }}" method="POST" onsubmit="return confirm('PENTING: Menghapus Hakim ini juga akan menghapus semua jadwal sidang dan riwayat presensi terkait. Yakin?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Hapus Hakim">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
