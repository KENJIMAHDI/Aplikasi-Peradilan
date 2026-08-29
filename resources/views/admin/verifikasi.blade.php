@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showVerifyModal: false, verifyUrl: '', selectedRegister: '', defaultNomorPerkara: '' }">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Verifikasi & Penjadwalan Perkara</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar pengajuan perkara mandiri masyarakat masuk melalui e-Court.</p>
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
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Nomor Register</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Penggugat</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Tergugat</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Klasifikasi</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Panjar Biaya</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Status Bayar</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Status Verifikasi</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($perkaras as $perkara)
                        <tr class="hover:bg-emerald-50/10 transition-colors">
                            <td class="px-6 py-4 font-bold text-emerald-700 text-sm">
                                {{ $perkara->nomor_register }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-semibold">{{ $perkara->penggugat }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">NIK: {{ $perkara->nik_penggugat ?: '-' }} | WA: {{ $perkara->no_wa_penggugat ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $perkara->tergugat }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $perkara->jenis_perdata }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-750">
                                Rp {{ number_format($perkara->nominal_panjar, 2, ',', '.') }}
                                <div class="text-[10px] text-gray-400 mt-0.5">VA BNI: {{ $perkara->nomor_va ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($perkara->status_bayar === 'lunas')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                        Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200 animate-pulse">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($perkara->status_verifikasi === 'terverifikasi')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Draft/Belum Verif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-y-1.5 whitespace-nowrap">
                                @if($perkara->status_bayar !== 'lunas')
                                    <form action="{{ route('admin.verifikasi.bayar', $perkara->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-1.5 px-3 rounded-lg text-xs transition-all shadow-sm">
                                            ✔ Konfirmasi Bayar Lunas
                                        </button>
                                    </form>
                                @endif

                                @if($perkara->status_verifikasi !== 'terverifikasi')
                                    <button 
                                        @click="showVerifyModal = true; verifyUrl = '/admin/verifikasi-perkara/{{ $perkara->id }}/verifikasi'; selectedRegister = '{{ $perkara->nomor_register }}'; defaultNomorPerkara = '{{ rand(100, 999) }}/Pdt.G/' + new Date().getFullYear() + '/PN.Smg'"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 px-3 rounded-lg text-xs transition-all shadow-sm">
                                        ⚖ Verifikasi & Jadwalkan
                                    </button>
                                @endif

                                @if($perkara->file_gugatan)
                                    <a href="{{ $perkara->file_gugatan }}" target="_blank" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-1.5 px-3 rounded-lg text-xs transition-all">
                                        📄 Surat Gugatan PDF
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500 font-medium">
                                Belum ada pengajuan perkara mandiri masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Verifikasi & Penjadwalan Modal -->
    <div x-show="showVerifyModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none" x-cloak>
        <div class="fixed inset-0 bg-black/50 transition-opacity"></div>
        <div class="relative w-full max-w-xl mx-auto my-6 z-50 p-4">
            <form :action="verifyUrl" method="POST" class="relative bg-white border border-gray-100 rounded-2xl shadow-2xl p-6 flex flex-col text-left">
                @csrf
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                    <h3 class="text-base font-bold text-gray-800">Verifikasi & Penjadwalan Sidang Pertama</h3>
                    <button type="button" @click="showVerifyModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 p-3 rounded-lg flex justify-between items-center">
                        <span class="text-xs text-gray-500 font-medium">Nomor Register e-Court</span>
                        <span class="text-sm font-bold text-emerald-700" x-text="selectedRegister"></span>
                    </div>

                    <!-- Nomor Perkara Resmi -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Nomor Perkara Resmi</label>
                        <input type="text" name="nomor_perkara_resmi" required :value="defaultNomorPerkara" placeholder="Contoh: 123/Pdt.G/2026/PN.Smg" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm outline-none">
                    </div>

                    <!-- Tanggal Sidang Pertama -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Tanggal Sidang Pertama</label>
                        <input type="datetime-local" name="tanggal_sidang_pertama" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm outline-none">
                    </div>

                    <!-- Ruang Sidang -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Ruang Sidang</label>
                        <select name="ruang_sidang_id" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm outline-none bg-white">
                            <option value="" disabled selected>Pilih Ruang Sidang</option>
                            @foreach($ruangSidangs as $ruang)
                                <option value="{{ $ruang->id }}">{{ $ruang->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Majelis Hakim -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Majelis Hakim (Hakim Ketua)</label>
                        <select name="hakim_id" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm outline-none bg-white">
                            <option value="" disabled selected>Pilih Hakim</option>
                            @foreach($hakims as $hakim)
                                <option value="{{ $hakim->id }}">{{ $hakim->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button type="button" @click="showVerifyModal = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-4 rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow transition-all">
                        Terbitkan Nomor & Jadwalkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
