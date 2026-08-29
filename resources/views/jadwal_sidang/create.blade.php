@extends('layouts.app')
@section('title', 'Tambah Jadwal Sidang')
@section('page_title', 'Tambah Jadwal Sidang')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Form Tambah Jadwal</h2>
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline text-sm">Kembali</a>
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

            <form action="{{ route('jadwal-sidang.store') }}" method="POST">
@csrf

                    
                
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nomor_perkara">
                        Nomor Perkara
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nomor_perkara" name="nomor_perkara" type="text" value="{{ old('nomor_perkara') }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="hakim_id">
                        Hakim (Ketua Majelis)
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="hakim_id" name="hakim_id" required>
                        <option value="">-- Pilih Hakim --</option>
                        @foreach($hakims as $hakim)
                            <option value="{{ $hakim->id }}" {{ old('hakim_id') == $hakim->id ? 'selected' : '' }}>{{ $hakim->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="ruang_sidang_id">
                        Ruang Sidang
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="ruang_sidang_id" name="ruang_sidang_id" required>
                        <option value="">-- Pilih Ruang Sidang --</option>
                        @foreach($ruangs as $ruang)
                            <option value="{{ $ruang->id }}" {{ old('ruang_sidang_id') == $ruang->id ? 'selected' : '' }}>{{ $ruang->nama_ruangan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="waktu_mulai">
                            Waktu Mulai
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="waktu_mulai" name="waktu_mulai" type="datetime-local" value="{{ old('waktu_mulai') }}" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="waktu_selesai">
                            Waktu Selesai
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="waktu_selesai" name="waktu_selesai" type="datetime-local" value="{{ old('waktu_selesai') }}" required>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-150" type="submit">
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
