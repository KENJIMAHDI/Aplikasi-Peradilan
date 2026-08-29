@extends('layouts.app')
@section('title', 'e-Court - Portal Peradilan Terpadu')
@section('page_title', 'Layanan e-Court (Perkara Perdata)')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Perkara e-Court</h2>
        <a href="{{ route('e-court.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm transition duration-150">
            + Daftar Perkara Baru
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                    <th class="py-3 px-6 border-b">No. Register</th>
                    <th class="py-3 px-6 border-b">Jenis</th>
                    <th class="py-3 px-6 border-b">Pihak</th>
                    <th class="py-3 px-6 border-b">Biaya Panjar</th>
                    <th class="py-3 px-6 border-b">Status Pembayaran</th>
                    <th class="py-3 px-6 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($perkaras as $perkara)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="py-3 px-6 font-medium">{{ $perkara->nomor_register }}</td>
                        <td class="py-3 px-6">{{ $perkara->jenis_perdata }}</td>
                        <td class="py-3 px-6">
                            <span class="block text-xs">P: {{ $perkara->penggugat }}</span>
                            <span class="block text-xs">T: {{ $perkara->tergugat }}</span>
                        </td>
                        <td class="py-3 px-6">Rp {{ number_format($perkara->nominal_panjar, 0, ',', '.') }}</td>
                        <td class="py-3 px-6">
                            @if($perkara->status_pembayaran == 'Lunas')
                                <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">Lunas</span>
                            @else
                                <span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs font-semibold">Belum Dibayar</span>
                            @endif
                        </td>
                        <td class="py-3 px-6">
                            <a href="#" class="text-blue-600 hover:underline text-xs">Detail</a>
                            @if($perkara->link_litigasi_online)
                                | <a href="{{ $perkara->link_litigasi_online }}" target="_blank" class="text-purple-600 hover:underline text-xs">Sidang Online</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 px-6 text-center text-gray-500">
                            Belum ada pendaftaran perkara e-Court.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
