@extends('layouts.app')
@section('title', 'Pengajuan e-Raterang')
@section('page_title', 'Form Pengajuan e-Raterang (Masyarakat)')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Form Permohonan Surat Keterangan</h2>
            <a href="{{ route('e-raterang.index') }}" class="text-blue-600 hover:underline text-sm">Ke Panel Petugas</a>
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

            <form action="{{ route('e-raterang.store') }}" method="POST">
@csrf

                    
                
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nik_pemohon">
                        NIK Pemohon (16 Digit)
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nik_pemohon" name="nik_pemohon" type="text" maxlength="16" value="{{ old('nik_pemohon') }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_pemohon">
                        Nama Lengkap (Sesuai KTP)
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nama_pemohon" name="nama_pemohon" type="text" value="{{ old('nama_pemohon') }}" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="jenis_surat">
                        Jenis Surat Keterangan
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="jenis_surat" name="jenis_surat" required>
                        <option value="">-- Pilih Surat --</option>
                        <option value="Tidak Pernah Dipidana">Surat Keterangan Tidak Pernah Dipidana</option>
                        <option value="Tidak Sedang Dicabut Hak Pilih">Surat Keterangan Tidak Sedang Dicabut Hak Pilih</option>
                        <option value="Tidak Memiliki Tanggungan Hutang">Surat Keterangan Tidak Memiliki Tanggungan Hutang</option>
                    </select>
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
