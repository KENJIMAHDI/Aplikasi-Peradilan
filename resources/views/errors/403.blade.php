<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen px-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-8 text-center">
        <div class="w-20 h-20 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m0 0v2m0-2h2m-2 0H10m-3.414-4.586a2 2 0 112.828-2.828L12 9.172l3.586-3.586a2 2 0 112.828 2.828L14.828 12l3.586 3.586a2 2 0 11-2.828 2.828L12 14.828l-3.586 3.586a2 2 0 11-2.828-2.828L8.586 12 5 8.414z"></path></svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Akses Ditolak</h1>
        <p class="text-gray-500 mb-8">Maaf, Anda tidak memiliki izin otorisasi (Role) yang diperlukan untuk mengakses halaman ini.</p>
        <div class="flex justify-center gap-3">
            <a href="javascript:history.back()" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                Kembali
            </a>
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold shadow-md shadow-emerald-200 hover:bg-emerald-700 transition-colors">
                Ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
