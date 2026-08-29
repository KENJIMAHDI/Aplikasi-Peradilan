@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Jadwal Sidang</h1>
        <p class="text-gray-500 text-sm mt-1">Manajemen jadwal dan status relaas perkara.</p>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl text-sm mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <p class="font-bold">Berhasil</p>
            <p class="mt-0.5 text-green-700">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div>
            <p class="font-bold">Gagal Menyimpan Data</p>
            <p class="mt-0.5 text-red-700">{{ $errors->first() }}</p>
        </div>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="{ 
         showModal: {{ $errors->any() ? 'true' : 'false' }}, 
         isEdit: {{ old('id') ? 'true' : 'false' }}, 
         editData: {
             id: '{{ old('id') }}',
             nomor_perkara: '{{ old('nomor_perkara') }}',
             waktu_mulai: '{{ old('waktu_mulai') }}',
             waktu_selesai: '{{ old('waktu_selesai') }}',
             hakim_id: '{{ old('hakim_id') }}',
             ruang_sidang_id: '{{ old('ruang_sidang_id') }}',
             status_relaas: '{{ old('status_relaas') }}'
         }, 
         searchQuery: '', 
         searchDate: '', 
         searchStatus: '' 
     }">
     
    <!-- Fitur Filter & Tambah -->
    <div class="p-4 border-b border-gray-100 flex flex-wrap gap-4 justify-between items-end bg-gray-50/50">
        <div class="flex flex-wrap gap-4 items-end flex-grow">
            <div class="flex-1 min-w-[200px] max-w-xs">
                <input type="text" x-model="searchQuery" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Cari Nomor Perkara...">
            </div>
            <div class="w-40">
                <input type="date" x-model="searchDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="w-48">
                <select x-model="searchStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">Semua Status Relaas</option>
                    <option value="Belum Dipanggil">Belum Dipanggil</option>
                    <option value="Relaas Siap/Patut">Relaas Siap/Patut</option>
                </select>
            </div>
        </div>
        @can('manage-perkara')
        <button @click="showModal = true; isEdit = false; editData = {}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm shadow-emerald-200 flex items-center gap-1 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Menambahkan Data
        </button>
        @endcan
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-white border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600">Nomor Perkara</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Waktu Mulai</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Waktu Selesai</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Ruang & Hakim</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Status Relaas</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Kehadiran Pihak</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($jadwals as $jadwal)
                <tr class="hover:bg-emerald-50/20 transition-colors duration-150" 
                    x-show="(searchQuery === '' || '{{ strtolower($jadwal->nomor_perkara) }}'.includes(searchQuery.toLowerCase())) && 
                            (searchDate === '' || '{{ substr($jadwal->waktu_mulai, 0, 10) }}' === searchDate) &&
                            (searchStatus === '' || '{{ $jadwal->status_relaas }}' === searchStatus)">
                    <td class="px-6 py-4 font-medium text-emerald-700">{{ $jadwal->nomor_perkara }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-gray-50 text-gray-700 border border-gray-200 rounded-md font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('d M Y, H:i') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-gray-50 text-gray-700 border border-gray-200 rounded-md font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('d M Y, H:i') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-900 font-medium">{{ $jadwal->ruangSidang ? $jadwal->ruangSidang->nama_ruangan : ($jadwal->ruang_sidang_id ? 'Ruang ID ' . $jadwal->ruang_sidang_id : '-') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $jadwal->hakim ? $jadwal->hakim->nama : ($jadwal->hakim_id ? 'Hakim ID ' . $jadwal->hakim_id : '-') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($jadwal->status_relaas === 'Relaas Siap/Patut')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border-green-200 border">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Siap/Patut
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border-red-200 border">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Belum Dipanggil
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-semibold text-gray-500 w-12">P:</span>
                                @if($jadwal->status_penggugat === 'Hadir & Siap Sidang' || $jadwal->status_penggugat === 'hadir')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-50 text-green-700 border border-green-200">Hadir & Siap Sidang</span>
                                @elseif(str_contains(strtolower($jadwal->status_penggugat), 'izin'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">{{ $jadwal->status_penggugat }}</span>
                                @elseif(str_contains(strtolower($jadwal->status_penggugat), 'sakit'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200">{{ $jadwal->status_penggugat }}</span>
                                @elseif($jadwal->status_penggugat === 'belum_hadir' || empty($jadwal->status_penggugat))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-700 border border-red-200">Belum Hadir</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">{{ $jadwal->status_penggugat }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-semibold text-gray-500 w-12">T:</span>
                                @if($jadwal->status_tergugat === 'Hadir & Siap Sidang' || $jadwal->status_tergugat === 'hadir')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-50 text-green-700 border border-green-200">Hadir & Siap Sidang</span>
                                @elseif(str_contains(strtolower($jadwal->status_tergugat), 'izin'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">{{ $jadwal->status_tergugat }}</span>
                                @elseif(str_contains(strtolower($jadwal->status_tergugat), 'sakit'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200">{{ $jadwal->status_tergugat }}</span>
                                @elseif($jadwal->status_tergugat === 'belum_hadir' || empty($jadwal->status_tergugat))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-700 border border-red-200">Belum Hadir</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">{{ $jadwal->status_tergugat }}</span>
                                @endif
                            </div>
                            <div class="mt-1">
                                @if($jadwal->status_kelengkapan === 'siap_sidang')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-600 text-white shadow-sm shadow-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Siap Sidang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-red-600 text-white shadow-sm shadow-red-200">
                                        Belum Lengkap
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                        @can('manage-perkara')
                        @if($jadwal->no_hp_penggugat || $jadwal->no_hp_tergugat)
                        <form action="{{ route('jadwal.panggil', $jadwal->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm shadow-emerald-100 mr-1 inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                Panggil WA
                            </button>
                        </form>
                        @endif
                        <button type="button" @click="showModal = true; isEdit = true; editData = {{ json_encode($jadwal) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold transition-colors mr-1">Edit</button>
                        <form action="/jadwal-sidang/{{ $jadwal->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada data jadwal sidang.</td>
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
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Edit Jadwal Sidang' : 'Tambah Jadwal Sidang'"></h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>

            <!-- Body Form CRUD -->
            <div>
                <form :action="isEdit ? '/jadwal-sidang/' + editData.id : '{{ route('jadwal.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="id" :value="isEdit ? editData.id : ''">
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nomor Perkara</label>
                            <input type="text" name="nomor_perkara" :value="isEdit ? editData.nomor_perkara : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Waktu Mulai</label>
                            <input type="datetime-local" name="waktu_mulai" :value="isEdit && editData.waktu_mulai ? editData.waktu_mulai.substring(0, 16) : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Waktu Selesai</label>
                            <input type="datetime-local" name="waktu_selesai" :value="isEdit && editData.waktu_selesai ? editData.waktu_selesai.substring(0, 16) : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Hakim</label>
                            <select name="hakim_id" x-model="editData.hakim_id" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="" disabled selected>Pilih Hakim</option>
                                @foreach($hakims as $hakim)
                                    <option value="{{ $hakim->id }}">{{ $hakim->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Ruang Sidang</label>
                            <select name="ruang_sidang_id" x-model="editData.ruang_sidang_id" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="" disabled selected>Pilih Ruang Sidang</option>
                                @foreach($ruangSidangs as $ruang)
                                    <option value="{{ $ruang->id }}">{{ $ruang->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div x-show="isEdit">
                            <label class="block text-sm font-semibold text-gray-700">Status Relaas</label>
                            <select name="status_relaas" :value="isEdit ? editData.status_relaas : 'Belum Dipanggil'" :disabled="!isEdit" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="Belum Dipanggil">Belum Dipanggil</option>
                                <option value="Relaas Siap/Patut">Relaas Siap/Patut</option>
                            </select>
                        </div>
                        <!-- Insert conflict errors via controller blade if any -->
                        @if($errors->has('conflict'))
                            <div class="bg-red-50 p-3 rounded text-red-700 text-sm mt-2 border border-red-200">
                                <p class="font-semibold">Konflik Jadwal Terdeteksi!</p>
                                <p>{{ $errors->first('conflict') }}</p>
                            </div>
                        @endif
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
</div>
@endsection
