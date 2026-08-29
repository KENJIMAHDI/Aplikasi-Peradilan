@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Modul e-Berpadu</h1>
        <p class="text-sm text-gray-500 mt-1">Panel Hakim untuk Persetujuan Penahanan/Penyitaan dari Penyidik/Penuntut Umum.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="eberpaduindexData()">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse text-gray-500">
            <thead class="bg-gray-50 border-b border-gray-100 uppercase tracking-wider text-gray-700">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600">Nomor Surat</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Instansi Pengaju</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Tersangka</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Jenis Permohonan</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Status Hakim</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi Approval</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <template x-for="p in permohonans" :key="p.id">
                    <tr class="hover:bg-emerald-50/20 transition-colors duration-150">
                        <td class="px-6 py-4 font-medium text-gray-900" x-text="p.nomor_surat"></td>
                        <td class="px-6 py-4" x-text="p.instansi_pengaju"></td>
                        <td class="px-6 py-4 font-medium" x-text="p.nama_tersangka"></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border-blue-200 border" x-text="p.jenis_permohonan"></span>
                        </td>
                        <td class="px-6 py-4">
                            <template x-if="p.status_persetujuan_hakim === 'Disetujui'">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border-green-200 border" x-text="p.status_persetujuan_hakim"></span>
                            </template>
                            <template x-if="p.status_persetujuan_hakim === 'Ditolak'">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border-red-200 border" x-text="p.status_persetujuan_hakim"></span>
                            </template>
                            <template x-if="p.status_persetujuan_hakim === 'Menunggu' || (p.status_persetujuan_hakim !== 'Disetujui' && p.status_persetujuan_hakim !== 'Ditolak')">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border-amber-200 border" x-text="p.status_persetujuan_hakim"></span>
                            </template>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <template x-if="p.status_persetujuan_hakim === 'Menunggu'">
                                <div class="flex justify-end gap-2">
                                    <button @click="updateStatus(p.id, 'Disetujui')" class="p-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded transition-colors" title="Setujui">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                    <button @click="updateStatus(p.id, 'Ditolak')" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded transition-colors" title="Tolak">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="p.status_persetujuan_hakim !== 'Menunggu'">
                                <span class="text-gray-400 italic">Telah diproses</span>
                            </template>
                        </td>
                    </tr>
                </template>
                <tr x-show="permohonans.length === 0">
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada permohonan masuk.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('eberpaduindexData', () => ({ 
        permohonans: @json($permohonans),
        
        async updateStatus(id, status) {
            if (!confirm('Apakah Anda yakin ingin mengubah status menjadi ' + status + '?')) return;
            try {
                const response = await fetch(`/e-berpadu/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: status })
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
        }
     }))
    })
</script>
@endsection
