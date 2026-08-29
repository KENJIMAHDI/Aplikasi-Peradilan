@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showVaModal: false, selectedVa: '', selectedNominal: '', selectedRegister: '' }">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Perkara Saya</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar perkara yang Anda daftarkan secara mandiri lewat e-Court.</p>
        </div>
        <a href="{{ route('perkara.register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl text-sm shadow transition-all">
            + Registrasi Perkara Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm font-semibold flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50 text-left">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Nomor Perkara/Register</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Tanggal Daftar</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Kategori Perkara</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Tergugat / Terlawan</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Timeline Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Panjar Biaya</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Status Bayar</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($perkaras as $perkara)
                        <tr class="hover:bg-emerald-50/10 transition-colors">
                            <td class="px-6 py-4 font-bold text-emerald-700 text-sm">
                                {{ $perkara->nomor_register }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $perkara->tanggal_daftar ? \Carbon\Carbon::parse($perkara->tanggal_daftar)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $perkara->jenis_perdata }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $perkara->tergugat }}</td>
                            <td class="px-6 py-4">
                                <!-- Timeline Status -->
                                <div class="flex items-center gap-1.5 text-[11px] font-bold">
                                    <span class="px-2 py-0.5 rounded {{ $perkara->status === 'Diajukan' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }}">Diajukan</span>
                                    <span class="text-gray-400">&rarr;</span>
                                    <span class="px-2 py-0.5 rounded {{ $perkara->status_verifikasi === 'terverifikasi' && $perkara->status !== 'Sidang' && $perkara->status !== 'Putus' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-500' }}">Terverifikasi</span>
                                    <span class="text-gray-400">&rarr;</span>
                                    <span class="px-2 py-0.5 rounded {{ $perkara->status === 'Sidang' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-500' }}">Sidang</span>
                                    <span class="text-gray-400">&rarr;</span>
                                    <span class="px-2 py-0.5 rounded {{ $perkara->status === 'Putus' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-500' }}">Putus</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                Rp {{ number_format($perkara->nominal_panjar, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($perkara->status_bayar === 'lunas')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                        Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 space-y-1.5">
                                @if($perkara->status_bayar !== 'lunas')
                                    <button 
                                        @click="showVaModal = true; selectedVa = '{{ $perkara->nomor_va }}'; selectedNominal = 'Rp {{ number_format($perkara->nominal_panjar, 0, ',', '.') }}'; selectedRegister = '{{ $perkara->nomor_register }}'"
                                        class="w-full text-center bg-amber-500 hover:bg-amber-600 text-white font-bold py-1 px-2.5 rounded-lg text-xs transition-all shadow-sm">
                                        💳 Bayar Panjar (VA)
                                    </button>
                                @endif
                                
                                @if($perkara->file_gugatan)
                                    <a href="{{ $perkara->file_gugatan }}" target="_blank" class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-1 px-2.5 rounded-lg text-xs transition-all">
                                        📄 Berkas Gugatan
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500 font-medium">
                                Belum ada pengajuan perkara mandiri hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Virtual Account Payment Modal -->
    <div x-show="showVaModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none" x-cloak>
        <div class="fixed inset-0 bg-black/50 transition-opacity"></div>
        <div class="relative w-full max-w-md mx-auto my-6 z-50 p-4">
            <div class="relative bg-white border border-gray-100 rounded-2xl shadow-2xl p-6 flex flex-col text-left">
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                    <h3 class="text-base font-bold text-gray-800">Pembayaran Panjar Perkara (e-Court)</h3>
                    <button type="button" @click="showVaModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-emerald-50/50 border border-emerald-100 p-4 rounded-xl text-center">
                        <span class="text-xs text-gray-500 font-medium uppercase tracking-wider block">Nomor Virtual Account (VA) BNI</span>
                        <span class="text-2xl font-extrabold text-emerald-700 block mt-1 tracking-wider" x-text="selectedVa"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-gray-400 block mb-0.5">Nomor Register</span>
                            <span class="font-bold text-gray-800" x-text="selectedRegister"></span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-gray-400 block mb-0.5">Nominal Panjar</span>
                            <span class="font-bold text-gray-800" x-text="selectedNominal"></span>
                        </div>
                    </div>

                    <div class="text-xs text-gray-500 leading-relaxed border-t border-gray-100 pt-4">
                        <p class="font-semibold text-gray-700 mb-1">Petunjuk Pembayaran Mandiri:</p>
                        <ol class="list-decimal pl-4 space-y-1">
                            <li>Gunakan aplikasi Mobile Banking atau ATM bank Anda.</li>
                            <li>Pilih menu <strong>Transfer / Pembayaran Virtual Account</strong>.</li>
                            <li>Masukkan nomor VA di atas (<span class="font-bold" x-text="selectedVa"></span>).</li>
                            <li>Periksa nominal harus sesuai dengan nominal panjar di atas.</li>
                            <li>Konfirmasi transaksi Anda. Pembayaran akan terverifikasi otomatis oleh Admin Kepaniteraan.</li>
                        </ol>
                    </div>
                </div>

                <button @click="showVaModal = false" class="mt-5 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow transition-all">
                    Selesai & Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
