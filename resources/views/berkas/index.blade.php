@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Berkas Perkara & Putusan</h1>
        <p class="text-gray-500 mt-1">Manajemen draf putusan, anonimisasi, dan publikasi direktori</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="{ showModal: false, showEditModal: false, isEdit: false, editData: {}, searchQuery: '' }">
     
    <!-- Header & Filter -->
    <div class="p-4 border-b border-gray-100 flex flex-wrap gap-4 justify-between items-center bg-gray-50/50">
        <div class="relative w-64 max-w-xs">
            <input type="text" x-model="searchQuery" class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Cari Nomor Perkara...">
        </div>
        @can('manage-perkara')
        <button @click="showModal = true; isEdit = false; editData = {}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm shadow-emerald-200 flex items-center gap-1 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Unggah Putusan Baru
        </button>
        @endcan
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-white border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600">Nomor Perkara</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">File Asli</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">File Anonim</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Status Anonimisasi</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($berkas as $item)
                <tr class="hover:bg-emerald-50/20 transition-colors duration-150"
                    x-show="searchQuery === '' || '{{ strtolower($item->nomor_perkara) }}'.includes(searchQuery.toLowerCase())">
                    <td class="px-6 py-4 font-medium text-emerald-700">{{ $item->nomor_perkara }}</td>
                    <td class="px-6 py-4">
                        @if($item->file_asli)
                            <a href="/storage/{{ $item->file_asli }}" target="_blank" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Lihat File
                            </a>
                        @else
                            <span class="text-gray-400 italic">Belum diunggah</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($item->file_asli)
                            <a href="/berkas-perkara/{{ $item->id }}/anonim" class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-800 font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Download Anonim
                            </a>
                        @else
                            <span class="text-gray-400 italic">Belum ada</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($item->is_anonim_selesai)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border-green-200 border">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border-yellow-200 border">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Proses
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @can('manage-perkara')
                        <button type="button" @click="showEditModal = true; isEdit = true; editData = {{ json_encode($item) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold transition-colors">Edit Status</button>
                        <form action="/berkas-perkara/{{ $item->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-50 mb-3 border border-gray-100">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p>Belum ada data berkas perkara.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form Tambah Berkas -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/50" 
         style="display: none;">
        
        <div class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl p-6 overflow-hidden my-auto border border-gray-100 max-h-[90vh] overflow-y-auto text-left" @click.away="showModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Unggah Dokumen Putusan</h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>

            <!-- Body Form CRUD -->
            <div>
                <form action="{{ route('berkas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nomor Perkara</label>
                            <input type="text" name="nomor_perkara" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">File Dokumen (PDF/Word/Excel)</label>
                            <input type="file" name="file_asli" accept=".pdf,.doc,.docx,.xls,.xlsx" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Maks. 10MB</p>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex flex-row-reverse border-t border-gray-100 pt-4 gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700 sm:w-auto">
                            Unggah File
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Form Edit Berkas -->
    <div x-show="showEditModal" 
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/50" 
         style="display: none;">
        
        <div class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl p-6 overflow-hidden my-auto border border-gray-100 max-h-[90vh] overflow-y-auto text-left" @click.away="showEditModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Update Status & File Putusan</h3>
                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>

            <!-- Body Form CRUD -->
            <div>
                <form :action="'/berkas-perkara/' + editData.id" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nomor Perkara</label>
                            <input type="text" :value="editData.nomor_perkara" disabled class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-gray-100 text-gray-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Status Anonimisasi</label>
                            <select name="is_anonim_selesai" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="0" :selected="!editData.is_anonim_selesai">Proses</option>
                                <option value="1" :selected="editData.is_anonim_selesai">Selesai</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Ganti File Dokumen (Opsional)</label>
                            <input type="file" name="file_asli" accept=".pdf,.doc,.docx,.xls,.xlsx" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengganti file. Maks. 10MB.</p>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex flex-row-reverse border-t border-gray-100 pt-4 gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700 sm:w-auto">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
