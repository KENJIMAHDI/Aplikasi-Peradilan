@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false, isEdit: false, editData: { status: 'Dalam Proses' } }">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bantuan Delegasi Panggilan Sidang</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar permohonan delegasi penyampaian relaas ke Pengadilan Negeri lain.</p>
        </div>
        @can('manage-perkara')
        <button type="button" @click="showModal = true; isEdit = false; editData = { status: 'Dalam Proses' }" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm shadow-emerald-200 flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Delegasi Baru
        </button>
        @endcan
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider">
                        <th class="p-4 font-medium">Nomor Perkara</th>
                        <th class="p-4 font-medium">PN Pengirim</th>
                        <th class="p-4 font-medium">PN Penerima</th>
                        <th class="p-4 font-medium">Tujuan Delegasi</th>
                        <th class="p-4 font-medium">Status Eksekusi</th>
                        <th class="p-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($delegasi as $d)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 font-medium text-gray-900">{{ $d->nomor_perkara }}</td>
                        <td class="p-4">{{ $d->pn_pengirim }}</td>
                        <td class="p-4">{{ $d->pn_penerima }}</td>
                        <td class="p-4">{{ $d->tujuan_delegasi }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $d->status == 'Selesai' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $d->status }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            @can('manage-perkara')
                            <button type="button" @click="showModal = true; isEdit = true; editData = {{ json_encode($d) }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm transition-colors">Edit</button>
                            <form action="/delegasi/{{ $d->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm transition-colors">Hapus</button>
                            </form>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">Belum ada data delegasi lintas pengadilan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit Delegasi -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/50" 
         style="display: none;">
        
        <div class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl p-6 overflow-hidden my-auto border border-gray-100 max-h-[90vh] overflow-y-auto text-left" @click.away="showModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit Delegasi' : 'Tambah Delegasi Baru'"></h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <!-- Body Form CRUD -->
            <div>
                <form :action="isEdit ? '/delegasi/' + editData.id : '{{ route('delegasi.store') }}'" method="POST">
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
                            <label class="block text-sm font-semibold text-gray-700">PN Pengirim</label>
                            <input type="text" name="pn_pengirim" :value="isEdit ? editData.pn_pengirim : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">PN Penerima</label>
                            <input type="text" name="pn_penerima" :value="isEdit ? editData.pn_penerima : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Tujuan Delegasi</label>
                            <input type="text" name="tujuan_delegasi" :value="isEdit ? editData.tujuan_delegasi : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Status</label>
                            <select name="status" :value="isEdit ? editData.status : 'Dalam Proses'" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="Dalam Proses">Dalam Proses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex flex-row-reverse border-t border-gray-100 pt-4 gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none sm:w-auto">
                            Simpan Data
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

