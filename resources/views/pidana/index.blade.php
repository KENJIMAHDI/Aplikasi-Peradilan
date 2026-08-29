@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Perkara Pidana</h1>
        <p class="text-gray-500 mt-1">Sistem Informasi Penelusuran Perkara (Pidana Biasa dan Pra Peradilan)</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="{ showModal: false, isEdit: false, editData: {}, tab: 'pidana_biasa', searchQuery: '' }">
     
    <!-- Tabs Header & Add Button -->
    <div class="border-b border-gray-100 flex justify-between items-center pr-6 bg-gray-50/30 overflow-x-auto">
        <div class="flex">
            <button @click="tab = 'pidana_biasa'" 
                    :class="{'border-emerald-500 text-emerald-600 font-semibold bg-white': tab === 'pidana_biasa', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'pidana_biasa'}" 
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all duration-200 outline-none">
                Pidana Biasa
            </button>
            <button @click="tab = 'pra_peradilan'" 
                    :class="{'border-emerald-500 text-emerald-600 font-semibold bg-white': tab === 'pra_peradilan', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'pra_peradilan'}" 
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all duration-200 outline-none">
                Pra Peradilan
            </button>
        </div>
        <button @click="showModal = true; isEdit = false; editData = { status: tab === 'pra_peradilan' ? 'Pra Peradilan' : 'Proses' }" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm shadow-green-200 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Menambahkan Data
        </button>
    </div>

    <!-- Tab Pidana Biasa -->
    <div x-show="tab === 'pidana_biasa'" class="p-0">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div class="relative w-64">
                <input type="text" x-model="searchQuery" class="block w-full pl-3 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" placeholder="Cari No. Perkara / Terdakwa...">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="bg-white border-b border-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-600">No. Perkara</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Terdakwa</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">JPU</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($perkara->where('status', '!=', 'Khusus')->where('status', '!=', 'Pra Peradilan') as $item)
                    <tr class="hover:bg-emerald-50/20 transition-colors duration-150" x-show="searchQuery === '' || '{{ strtolower($item->nomor_perkara) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($item->terdakwa) }}'.includes(searchQuery.toLowerCase())">
                        <td class="px-6 py-4 font-medium text-emerald-700">{{ $item->nomor_perkara }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">{{ $item->terdakwa }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $item->jaksa }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $item->status === 'Proses' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : ($item->status === 'Tuntutan' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button type="button" @click="showModal = true; isEdit = true; editData = {{ json_encode($item) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold transition-colors">Edit</button>
                            <form action="/pidana/{{ $item->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold transition-colors">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada perkara pidana biasa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab Pra Peradilan -->
    <div x-show="tab === 'pra_peradilan'" class="p-0" style="display: none;">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div class="relative w-64">
                <input type="text" x-model="searchQuery" class="block w-full pl-3 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" placeholder="Cari Pra Peradilan...">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="bg-white border-b border-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-600">No. Perkara</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Pemohon</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Termohon</th>
                        <th class="px-6 py-4 font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($perkara->where('status', 'Pra Peradilan') as $item)
                    <tr class="hover:bg-emerald-50/20 transition-colors duration-150" x-show="searchQuery === '' || '{{ strtolower($item->nomor_perkara) }}'.includes(searchQuery.toLowerCase())">
                        <td class="px-6 py-4 font-medium text-emerald-700">{{ $item->nomor_perkara }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">{{ $item->terdakwa }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $item->jaksa }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border bg-purple-50 text-purple-700 border-purple-200">{{ $item->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button type="button" @click="showModal = true; isEdit = true; editData = {{ json_encode($item) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold transition-colors">Edit</button>
                            <form action="/pidana/{{ $item->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold transition-colors">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada perkara pra peradilan.</td>
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
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Edit Data Perkara' : 'Tambah Data Perkara'"></h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>

            <!-- Body Form CRUD -->
            <div>
                <form :action="isEdit ? '/pidana/' + editData.id : '{{ route('pidana.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nomor Perkara</label>
                            <input type="text" name="nomor_perkara" :value="isEdit ? editData.nomor_perkara : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nama Terdakwa / Pemohon</label>
                            <input type="text" name="terdakwa" :value="isEdit ? editData.terdakwa : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">JPU / Termohon</label>
                            <input type="text" name="jaksa" :value="isEdit ? editData.jaksa : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Pasal Dakwaan</label>
                            <input type="text" name="pasal" :value="isEdit ? editData.pasal : ''" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Status</label>
                            <select name="status" x-model="editData.status" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="Proses">Proses</option>
                                <option value="Tuntutan">Tuntutan</option>
                                <option value="Putusan">Putusan</option>
                                <option value="Pra Peradilan">Pra Peradilan</option>
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
</div>
@endsection
