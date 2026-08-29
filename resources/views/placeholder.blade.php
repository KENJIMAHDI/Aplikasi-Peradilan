@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center h-full min-h-[400px]">
    <div class="text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">{{ $title ?? 'Modul' }}</h2>
        <p class="text-gray-500 mt-2">Halaman ini sedang dalam tahap pengembangan (Under Construction).</p>
        <a href="{{ route('dashboard') }}" class="inline-block mt-6 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors text-sm font-medium">
            &larr; Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
