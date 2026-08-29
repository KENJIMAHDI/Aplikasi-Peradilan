@extends('layouts.app')
@section('title', 'Pendaftaran e-Court')
@section('page_title', 'Pendaftaran Perkara e-Court Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Formulir Pendaftaran</h2>
            <a href="{{ route('e-court.index') }}" class="text-blue-600 hover:underline text-sm">Kembali</a>
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

            <form action="{{ route('e-court.store') }}" method="POST" id="form-pendaftaran">
@csrf

                    
                
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="jenis_perdata">
                        Jenis Perkara Perdata
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="jenis_perdata" name="jenis_perdata" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Gugatan Wanprestasi">Gugatan Wanprestasi</option>
                        <option value="Gugatan Perbuatan Melawan Hukum">Gugatan Perbuatan Melawan Hukum</option>
                        <option value="Permohonan Ganti Nama">Permohonan Ganti Nama</option>
                        <option value="Permohonan Ahli Waris">Permohonan Ahli Waris</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="penggugat">
                        Nama Penggugat / Pemohon
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="penggugat" name="penggugat" type="text" value="{{ old('penggugat') }}" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tergugat">
                        Nama Tergugat / Termohon (Tulis "Tidak Ada" jika Permohonan Tunggal)
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="tergugat" name="tergugat" type="text" value="{{ old('tergugat') }}" required>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
                    <h4 class="font-bold text-blue-800 mb-2">Estimasi Panjar Biaya Perkara (e-Skum)</h4>
                    <p class="text-sm text-blue-700">Berdasarkan kalkulator radius, estimasi biaya panjar untuk pendaftaran online ini adalah: <strong class="text-lg">Rp 700.000</strong></p>
                    <p class="text-xs text-blue-600 mt-1">*Biaya ini merupakan estimasi awal dan Virtual Account akan diterbitkan setelah pendaftaran berhasil.</p>
                </div>

                <div class="flex items-center justify-end">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-150" type="submit">
                        Daftar & Terbitkan e-SKUM
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
