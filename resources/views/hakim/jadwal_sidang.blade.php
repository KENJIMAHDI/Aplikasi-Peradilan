@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showPutusanModal: false, putusanUrl: '', selectedCase: '' }">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Jadwal Sidang & Putusan Hakim</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar perkara persidangan yang ditugaskan kepada Majelis Anda.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm font-semibold flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm font-semibold flex items-center gap-3">
            <span>❌</span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50 text-left">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Nomor Perkara</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Waktu Sidang</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Ruang Sidang</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Kehadiran Penggugat</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Kehadiran Tergugat</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Kelengkapan</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Status Sidang</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-emerald-50/10 transition-colors">
                            <td class="px-6 py-4 font-bold text-emerald-700 text-sm">
                                {{ $schedule->nomor_perkara }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                {{ $schedule->waktu_mulai ? \Carbon\Carbon::parse($schedule->waktu_mulai)->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ $schedule->ruangSidang->nama_ruangan ?? 'Ruang Sidang' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($schedule->status_penggugat === 'Hadir & Siap Sidang' || $schedule->status_penggugat === 'hadir')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-50 text-green-700 border border-green-200">Hadir & Siap Sidang</span>
                                @elseif(str_contains(strtolower($schedule->status_penggugat), 'izin'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">{{ $schedule->status_penggugat }}</span>
                                @elseif(str_contains(strtolower($schedule->status_penggugat), 'sakit'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200">{{ $schedule->status_penggugat }}</span>
                                @elseif($schedule->status_penggugat === 'belum_hadir' || empty($schedule->status_penggugat))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-700 border border-red-200">Belum Hadir</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">{{ $schedule->status_penggugat }}</span>
                                @endif
                                @if($schedule->no_hp_penggugat)
                                    <div class="text-[10px] text-gray-400 mt-0.5 font-mono">WA: {{ $schedule->no_hp_penggugat }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($schedule->status_tergugat === 'Hadir & Siap Sidang' || $schedule->status_tergugat === 'hadir')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-50 text-green-700 border border-green-200">Hadir & Siap Sidang</span>
                                @elseif(str_contains(strtolower($schedule->status_tergugat), 'izin'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">{{ $schedule->status_tergugat }}</span>
                                @elseif(str_contains(strtolower($schedule->status_tergugat), 'sakit'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200">{{ $schedule->status_tergugat }}</span>
                                @elseif($schedule->status_tergugat === 'belum_hadir' || empty($schedule->status_tergugat))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-700 border border-red-200">Belum Hadir</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">{{ $schedule->status_tergugat }}</span>
                                @endif
                                @if($schedule->no_hp_tergugat)
                                    <div class="text-[10px] text-gray-400 mt-0.5 font-mono">WA: {{ $schedule->no_hp_tergugat }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($schedule->status_kelengkapan === 'siap_sidang')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-600 text-white shadow-sm">
                                        Siap Sidang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-red-600 text-white shadow-sm">
                                        Belum Lengkap
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($schedule->status === 'PUTUS')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                        Putus (Selesai)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        Sidang Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-y-1.5 whitespace-nowrap">
                                @if($schedule->status !== 'PUTUS')
                                    <button 
                                        @click="showPutusanModal = true; putusanUrl = '/hakim/jadwal-sidang/{{ $schedule->id }}/putusan'; selectedCase = '{{ $schedule->nomor_perkara }}'"
                                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded-lg text-xs transition-all shadow-sm">
                                        ⚖ Upload Putusan (Putus)
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500 font-medium">
                                Belum ada sidang yang ditugaskan kepada Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload Putusan Modal -->
    <div x-show="showPutusanModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none" x-cloak>
        <div class="fixed inset-0 bg-black/50 transition-opacity"></div>
        <div class="relative w-full max-w-md mx-auto my-6 z-50 p-4">
            <form :action="putusanUrl" method="POST" enctype="multipart/form-data" class="relative bg-white border border-gray-100 rounded-2xl shadow-2xl p-6 flex flex-col text-left">
                @csrf
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                    <h3 class="text-base font-bold text-gray-800">Upload Putusan & Selesaikan Perkara</h3>
                    <button type="button" @click="showPutusanModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 p-3 rounded-lg flex justify-between items-center">
                        <span class="text-xs text-gray-500 font-medium">Perkara</span>
                        <span class="text-sm font-bold text-emerald-700" x-text="selectedCase"></span>
                    </div>

                    <!-- Upload File Putusan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Pilih File Putusan Asli (PDF)</label>
                        <input type="file" name="file_putusan" accept=".pdf" required 
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-lg">
                    </div>

                    <div class="text-xs text-amber-700 bg-amber-50 border border-amber-100 p-3 rounded-lg">
                        <strong>Perhatian:</strong> Mengunggah draf putusan ini akan otomatis merubah status perkara di SIPP menjadi <strong>Putus</strong> dan menghentikan alur persidangan perkara ini.
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button type="button" @click="showPutusanModal = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-4 rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-750 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow transition-all">
                        Unggah & Putus Perkara
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
