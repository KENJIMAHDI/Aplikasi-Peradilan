@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Modul e-Raterang</h1>
        <p class="text-sm text-gray-500 mt-1">Verifikasi pengajuan Surat Keterangan Publik (Tidak Pernah Dipidana, dll).</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="eraterangindexData()">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse text-gray-500">
            <thead class="bg-gray-50 border-b border-gray-100 uppercase tracking-wider text-gray-700">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600">Nomor Permohonan</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">NIK / Nama Pemohon</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Jenis Surat</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Status Verifikasi</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <template x-for="p in permohonans" :key="p.id">
                    <tr class="hover:bg-emerald-50/20 transition-colors duration-150">
                        <td class="px-6 py-4 font-medium text-gray-900" x-text="p.nomor_permohonan"></td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-800" x-text="p.nama_pemohon"></p>
                            <p class="text-xs text-gray-500" x-text="p.nik_pemohon"></p>
                        </td>
                        <td class="px-6 py-4" x-text="p.jenis_surat"></td>
                        <td class="px-6 py-4">
                            <template x-if="p.status_verifikasi === 'Selesai'">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 border-green-200 border" x-text="p.status_verifikasi"></span>
                            </template>
                            <template x-if="p.status_verifikasi !== 'Selesai'">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border-amber-200 border" x-text="p.status_verifikasi"></span>
                            </template>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <template x-if="p.status_verifikasi === 'Belum Diverifikasi'">
                                <button @click="verifyPermohonan(p.id)" class="bg-emerald-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-emerald-700 transition-colors">
                                    Verifikasi
                                </button>
                            </template>
                            <template x-if="p.status_verifikasi !== 'Belum Diverifikasi'">
                                <button @click="printSurat(p.id)" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-indigo-700 transition-colors inline-flex items-center gap-1 ml-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak
                                </button>
                            </template>
                        </td>
                    </tr>
                </template>
                <tr x-show="permohonans.length === 0">
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada permohonan masuk.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('eraterangindexData', () => ({ 
        permohonans: @json($permohonans),
        
        async verifyPermohonan(id) {
            if (!confirm('Verifikasi permohonan Surat Keterangan ini?')) return;
            try {
                const response = await fetch(`/e-raterang/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const resData = await response.json();
                if (response.ok) {
                    const index = this.permohonans.findIndex(p => p.id === id);
                    if (index !== -1) {
                        this.permohonans[index] = resData.data;
                    }
                } else {
                    alert(resData.message || 'Terjadi kesalahan.');
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan koneksi.');
            }
        },

        printSurat(id) {
            window.open(`/e-raterang/${id}/print`, '_blank');
        }
     }))
    })
</script>
@endsection
