@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <div class="mb-6 border-b border-gray-100 pb-4">
            <h2 class="text-xl font-bold text-gray-800">Registrasi Perkara Baru Mandiri</h2>
            <p class="text-xs text-gray-500 mt-1">Isi formulir di bawah ini untuk mengajukan pendaftaran gugatan / permohonan baru secara mandiri.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <p class="font-bold">Pengajuan Gagal</p>
                    <p class="mt-0.5 text-red-700">{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('perkara.store_mandiri') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Jenis Perkara -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider border-b pb-1">Klasifikasi Perkara</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Kategori Perkara</label>
                        <select name="kategori" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                            <option value="perdata_gugatan">Perdata Gugatan</option>
                            <option value="perdata_permohonan">Perdata Permohonan</option>
                            <option value="phi">Perselisihan Hubungan Industrial (PHI)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Posisi Anda (Pihak)</label>
                        <select name="posisi_pihak" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                            <option value="penggugat">Penggugat / Pemohon</option>
                            <option value="kuasa_hukum">Kuasa Hukum / Pengacara</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Identitas Penggugat -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider border-b pb-1">Identitas Penggugat / Pemohon</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">NIK Penggugat</label>
                        <input type="text" name="nik_penggugat" required placeholder="Contoh: 3374012345670001" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">No. WhatsApp Aktif</label>
                        <input type="text" name="no_wa_penggugat" required placeholder="Contoh: 081234567890" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Alamat Domisili Penggugat</label>
                    <textarea name="alamat_penggugat" required rows="2" placeholder="Alamat lengkap sesuai KTP..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>
            </div>

            <!-- Identitas Pihak Tergugat/Termohon -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider border-b pb-1">Identitas Pihak Terlawan / Tergugat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Nama Tergugat / Termohon</label>
                        <input type="text" name="nama_tergugat" required placeholder="Nama Lengkap Tergugat" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Alamat Tergugat / Termohon</label>
                        <input type="text" name="alamat_tergugat" required placeholder="Alamat Domisili Tergugat" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>
            </div>

            <!-- Upload Dokumen Berkases -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider border-b pb-1">Upload Berkas Pendukung</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Upload File KTP Penggugat (JPG/PNG)</label>
                        <input type="file" name="file_ktp" accept="image/*" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Upload File Surat Gugatan / Permohonan (PDF)</label>
                        <input type="file" name="dokumen_gugatan" accept=".pdf" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow text-sm transition-all">
                Submit & Ajukan Registrasi Perkara
            </button>
        </form>
    </div>
</div>
@endsection
