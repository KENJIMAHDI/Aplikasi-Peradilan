@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Perkara Pidana Khusus (Tipikor/Anak)</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar perkara tindak pidana korupsi, narkotika, dan anak.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="{ showModal: false, isEdit: false, editData: {}, searchQuery: '' }">
     
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div class="relative w-64">
            <input type="text" x-model="searchQuery" class="block w-full pl-3 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" placeholder="Cari No. Perkara / Terdakwa...">
        </div>
        <button @click="showModal = true; isEdit = false; editData = {}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm shadow-emerald-200 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Menambahkan Data
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-500">
            <thead class="bg-white border-b border-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600">No. Perkara</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Terdakwa</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">JPU</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Pasal Dakwaan</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Status Sidang</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($perkara as $item)
                <tr class="hover:bg-emerald-50/20 transition-colors duration-150" x-show="searchQuery === '' || '{{ strtolower($item->nomor_perkara) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($item->terdakwa) }}'.includes(searchQuery.toLowerCase())">
                    <td class="px-6 py-4 font-medium text-emerald-700">{{ $item->nomor_perkara }}</td>
                    <td class="px-6 py-4 text-gray-900 font-medium">{{ $item->terdakwa }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->jaksa }}</td>
                    <td class="px-6 py-4 text-red-600 font-medium">{{ $item->pasal }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border bg-amber-50 text-amber-700 border-amber-200">{{ $item->status }}</span>
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
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada perkara pidana khusus.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Wrapper -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/50" 
         style="display: none;">
        
        <div class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl p-6 overflow-hidden my-auto border border-gray-100 max-h-[90vh] overflow-y-auto text-left" @click.away="showModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Edit Pidana Khusus' : 'Tambah Pidana Khusus'"></h3>
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
                        <!-- Status is always Khusus -->
                        <input type="hidden" name="status" value="Khusus">
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
