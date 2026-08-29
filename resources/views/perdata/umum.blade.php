@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Perkara Perdata Umum</h1>
        <p class="text-gray-500 mt-1">Sistem Informasi Penelusuran Perkara (Gugatan dan Permohonan)</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="{ 
         showModal: false, 
         isEdit: false, 
         editData: { status: 'Diajukan', jenis_perdata: 'Gugatan', nominal_panjar: 0, status_pembayaran: 'Belum Dibayar' }, 
         tab: (new URLSearchParams(window.location.search).get('tab')) || '{{ session('active_tab', 'gugatan') }}', 
         searchQuery: '' 
     }">
     
    <!-- Tabs Header & Add Button -->
    <div class="border-b border-gray-100 flex justify-between items-center pr-6 bg-gray-50/30 overflow-x-auto">
        <div class="flex">
            <button type="button"
                    @click="tab = 'gugatan'; const url = new URL(window.location); url.searchParams.set('tab', 'gugatan'); window.history.replaceState({}, '', url);" 
                    :class="{'border-emerald-500 text-emerald-600 font-semibold bg-white': tab === 'gugatan', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'gugatan'}" 
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all duration-200 outline-none">
                Register Gugatan
            </button>
            <button type="button"
                    @click="tab = 'permohonan'; const url = new URL(window.location); url.searchParams.set('tab', 'permohonan'); window.history.replaceState({}, '', url);" 
                    :class="{'border-emerald-500 text-emerald-600 font-semibold bg-white': tab === 'permohonan', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'permohonan'}" 
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all duration-200 outline-none">
                Register Permohonan
            </button>
        </div>
        @can('manage-perkara')
        <button type="button" 
                @click="showModal = true; isEdit = false; editData = { status: 'Diajukan', jenis_perdata: tab === 'permohonan' ? 'Permohonan' : 'Gugatan', tanggal_daftar: '{{ date('Y-m-d') }}', nominal_panjar: 0, status_pembayaran: 'Belum Dibayar' }" 
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm shadow-emerald-200 flex items-center gap-1 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Menambahkan Data
        </button>
        @endcan
    </div>

    <!-- Tab Gugatan -->
    <div x-show="tab === 'gugatan'" class="p-0">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div class="relative w-64">
                <input type="text" x-model="searchQuery" class="block w-full pl-3 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm bg-white" placeholder="Cari No. Perkara...">
            </div>
            <span class="text-xs text-gray-400 font-medium">Menampilkan data Gugatan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="bg-white border-b border-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-600">No. Perkara</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Tanggal Daftar</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Penggugat</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Tergugat</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Panjar Biaya</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Status Bayar</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($perkara->where('jenis_perdata', 'Gugatan') as $item)
                    <tr class="hover:bg-emerald-50/20 transition-colors duration-150" x-show="searchQuery === '' || '{{ strtolower($item->nomor_register) }} {{ strtolower($item->penggugat) }} {{ strtolower($item->tergugat) }}'.includes(searchQuery.toLowerCase())">
                        <td class="px-6 py-4 font-medium text-emerald-700">{{ $item->nomor_register }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $item->tanggal_daftar ? \Carbon\Carbon::parse($item->tanggal_daftar)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">{{ $item->penggugat }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">{{ $item->tergugat ?: '-' }}</td>
                        <td class="px-6 py-4 text-gray-600 font-semibold">Rp {{ number_format($item->nominal_panjar, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $item->status_pembayaran === 'Lunas' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                {{ $item->status_pembayaran ?: 'Belum Dibayar' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $item->status === 'Diajukan' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : ($item->status === 'Sedang Di Proses' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @can('manage-perkara')
                            <button type="button" @click="showModal = true; isEdit = true; editData = {{ json_encode($item) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold transition-colors">Edit</button>
                            <form action="/perdata-umum/{{ $item->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perkara ini?')">
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
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">Belum ada data register gugatan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab Permohonan -->
    <div x-show="tab === 'permohonan'" class="p-0" style="display: none;">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div class="relative w-64">
                <input type="text" x-model="searchQuery" class="block w-full pl-3 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm bg-white" placeholder="Cari No. Perkara...">
            </div>
            <span class="text-xs text-gray-400 font-medium">Menampilkan data Permohonan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="bg-white border-b border-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-600">No. Perkara</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Tanggal Daftar</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Pemohon</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Tergugat</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Panjar Biaya</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Status Bayar</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($perkara->where('jenis_perdata', 'Permohonan') as $item)
                    <tr class="hover:bg-emerald-50/20 transition-colors duration-150" x-show="searchQuery === '' || '{{ strtolower($item->nomor_register) }} {{ strtolower($item->penggugat) }}'.includes(searchQuery.toLowerCase())">
                        <td class="px-6 py-4 font-medium text-emerald-700">{{ $item->nomor_register }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $item->tanggal_daftar ? \Carbon\Carbon::parse($item->tanggal_daftar)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">{{ $item->penggugat }}</td>
                        <td class="px-6 py-4 text-gray-500 italic font-normal">{{ $item->tergugat ?: '-' }}</td>
                        <td class="px-6 py-4 text-gray-600 font-semibold">Rp {{ number_format($item->nominal_panjar, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $item->status_pembayaran === 'Lunas' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                {{ $item->status_pembayaran ?: 'Belum Dibayar' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $item->status === 'Diajukan' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : ($item->status === 'Sedang Di Proses' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @can('manage-perkara')
                            <button type="button" @click="showModal = true; isEdit = true; editData = {{ json_encode($item) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold transition-colors">Edit</button>
                            <form action="/perdata-umum/{{ $item->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perkara ini?')">
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
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">Belum ada data register permohonan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Wrapper (Tepat di Tengah & Tidak Terpotong Layout) -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/50" 
         style="display: none;">
        
        <div class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl p-6 overflow-hidden my-auto border border-gray-100 max-h-[90vh] overflow-y-auto text-left" @click.away="showModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Edit Data Register' : ('Tambah Register ' + (tab === 'permohonan' ? 'Permohonan' : 'Gugatan'))"></h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>

            <!-- Body Form CRUD -->
            <div>
                <form :action="isEdit ? '/perdata-umum/' + editData.id : '{{ route('perdata-umum.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>
                    
                    <input type="hidden" name="jenis_perdata" :value="isEdit ? editData.jenis_perdata : (tab === 'permohonan' ? 'Permohonan' : 'Gugatan')">
                    <input type="hidden" name="kategori" :value="isEdit ? editData.jenis_perdata : (tab === 'permohonan' ? 'Permohonan' : 'Gugatan')" value="{{ request('tab', 'gugatan') }}">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nomor Perkara</label>
                            <input type="text" name="nomor_perkara" :value="isEdit ? editData.nomor_register : ''" required placeholder="Contoh: 123/Pdt.P/2026/PN.Smg" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Tanggal Daftar</label>
                            <input type="date" name="tanggal_daftar" :value="isEdit ? (editData.tanggal_daftar ? editData.tanggal_daftar.substring(0,10) : '') : '{{ date('Y-m-d') }}'" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700" x-text="(isEdit ? editData.jenis_perdata : (tab === 'permohonan' ? 'Permohonan' : 'Gugatan')) === 'Permohonan' ? 'Pihak Pemohon' : 'Pihak Penggugat'"></label>
                            <input type="text" name="penggugat" :value="isEdit ? editData.penggugat : ''" required placeholder="Nama Lengkap Pemohon / Penggugat" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div x-show="(isEdit ? editData.jenis_perdata : (tab === 'permohonan' ? 'Permohonan' : 'Gugatan')) === 'Gugatan'">
                            <label class="block text-sm font-semibold text-gray-700">Pihak Tergugat</label>
                            <input type="text" name="tergugat" :value="isEdit ? editData.tergugat : ''" :disabled="(isEdit ? editData.jenis_perdata : (tab === 'permohonan' ? 'Permohonan' : 'Gugatan')) !== 'Gugatan'" placeholder="Nama Lengkap Tergugat" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nominal Panjar (Rp)</label>
                            <input type="number" name="nominal_panjar" :value="isEdit ? editData.nominal_panjar : '0'" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Status Pembayaran</label>
                            <select name="status_pembayaran" x-model="editData.status_pembayaran" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="Belum Dibayar">Belum Dibayar</option>
                                <option value="Lunas">Lunas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Status Perkara</label>
                            <select name="status" x-model="editData.status" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="Diajukan">Diajukan</option>
                                <option value="Sedang Di Proses">Sedang Di Proses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Jadwal Sidang Online (Opsional)</label>
                            <input type="datetime-local" name="jadwal_sidang_online" :value="isEdit && editData.jadwal_sidang_online ? editData.jadwal_sidang_online.substring(0, 16) : ''" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Link Litigasi Online (Opsional)</label>
                            <input type="url" name="link_litigasi_online" :value="isEdit ? editData.link_litigasi_online : ''" placeholder="https://meet.google.com/..." class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
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
</div>
@endsection
