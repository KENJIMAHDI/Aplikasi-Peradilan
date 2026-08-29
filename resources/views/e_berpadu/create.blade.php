@extends('layouts.app')
@section('title', 'Pengajuan e-Berpadu')
@section('page_title', 'Form Pengajuan e-Berpadu (Penyidik/Penuntut)')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Form Pengajuan Izin/Persetujuan</h2>
            <a href="{{ route('e-berpadu.index') }}" class="text-blue-600 hover:underline text-sm">Ke Panel Hakim</a>
        </div>
        
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('e-berpadu.store') }}" method="POST">
@csrf

                    
                
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="instansi_pengaju">
                        Instansi Pengaju
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="instansi_pengaju" name="instansi_pengaju" required>
                        <option value="">-- Pilih Instansi --</option>
                        <option value="Kepolisian Resor (Polres)">Kepolisian Resor (Polres)</option>
                        <option value="Kepolisian Sektor (Polsek)">Kepolisian Sektor (Polsek)</option>
                        <option value="Kejaksaan Negeri (Kejari)">Kejaksaan Negeri (Kejari)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="jenis_permohonan">
                        Jenis Permohonan
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="jenis_permohonan" name="jenis_permohonan" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Izin Penahanan">Izin Penahanan</option>
                        <option value="Perpanjangan Penahanan">Perpanjangan Penahanan</option>
                        <option value="Izin Penggeledahan">Izin Penggeledahan</option>
                        <option value="Izin Penyitaan">Izin Penyitaan</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_tersangka">
                        Nama Tersangka
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nama_tersangka" name="nama_tersangka" type="text" value="{{ old('nama_tersangka') }}" required>
                </div>

                <div class="flex items-center justify-end">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-150" type="submit">
                        Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
