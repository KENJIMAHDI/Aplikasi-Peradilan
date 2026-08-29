<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Peradilan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- AlpineJS with Collapse Plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans transition-all duration-300" x-data="{ sidebarOpen: true, highContrast: false, textSize: 'normal' }" :class="{ 'bg-black text-white': highContrast, 'text-lg': textSize === 'large', 'text-xl': textSize === 'xlarge' }">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar Kiri -->
        <aside :class="sidebarOpen ? 'w-72' : 'w-20'" class="bg-emerald-900 text-emerald-100 flex-shrink-0 transition-all duration-300 flex flex-col shadow-2xl z-20">
            <!-- Header Sidebar -->
            <div class="h-16 flex items-center justify-center bg-emerald-950 border-b border-emerald-800 px-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-bold text-lg tracking-wider text-white whitespace-nowrap transition-opacity duration-300">APLIKASI PERADILAN</span>
                </div>
            </div>

            <!-- Main Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-3 custom-scrollbar">
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-800 text-white shadow-inner' : 'hover:bg-emerald-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Dashboard</span>
                </a>

                @if(auth()->user()->role === 'masyarakat')
                <a href="{{ route('perkara.register') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('perkara.register') ? 'bg-emerald-800 text-white shadow-inner' : 'hover:bg-emerald-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Registrasi Perkara Baru</span>
                </a>

                <a href="{{ route('perkara.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('perkara.index') ? 'bg-emerald-800 text-white shadow-inner' : 'hover:bg-emerald-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Data Perkara Saya</span>
                </a>
                @endif

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.verifikasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.verifikasi.*') ? 'bg-emerald-800 text-white shadow-inner' : 'hover:bg-emerald-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Verifikasi Perkara</span>
                </a>
                @endif

                @can('manage-users')
                <!-- User Management -->
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-emerald-800 text-white shadow-inner' : 'hover:bg-emerald-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">User Management</span>
                </a>
                @endcan

                @can('manage-perkara')
                <!-- Perdata Accordion -->
                <div x-data="{ open: false }" class="mt-2">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-emerald-800/50 hover:text-white transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Perdata</span>
                        </div>
                        <svg x-show="sidebarOpen" :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="pl-11 pr-3 py-2 space-y-1 bg-emerald-950/30 rounded-b-lg">
                        <a href="{{ route('perdata.umum') }}" class="block text-sm py-1.5 text-emerald-300 hover:text-white hover:translate-x-1 transition-transform">Perdata Umum</a>
                        <a href="{{ route('perdata.khusus') }}" class="block text-sm py-1.5 text-emerald-300 hover:text-white hover:translate-x-1 transition-transform">Perdata Khusus</a>
                    </div>
                </div>

                <!-- Pidana Accordion -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-emerald-800/50 hover:text-white transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Pidana</span>
                        </div>
                        <svg x-show="sidebarOpen" :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="pl-11 pr-3 py-2 space-y-1 bg-emerald-950/30 rounded-b-lg">
                        <a href="{{ route('pidana') }}" class="block text-sm py-1.5 text-emerald-300 hover:text-white hover:translate-x-1 transition-transform">Pidana Biasa</a>
                        <a href="{{ route('pidana.khusus') }}" class="block text-sm py-1.5 text-emerald-300 hover:text-white hover:translate-x-1 transition-transform">Pidana Khusus</a>
                    </div>
                </div>
                @endcan

                @can('manage-hakim')
                @if(auth()->user()->role === 'hakim')
                <a href="{{ route('hakim.jadwal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('hakim.jadwal.*') ? 'bg-emerald-800 text-white shadow-inner' : 'hover:bg-emerald-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Jadwal Sidang Hakim</span>
                </a>
                @endif
                <a href="{{ route('jadwal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('jadwal.*') ? 'bg-emerald-800 text-white shadow-inner' : 'hover:bg-emerald-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Jadwal Sidang</span>
                </a>

                <a href="{{ route('kehadiran.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-emerald-800/50 hover:text-white transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Kehadiran Hakim</span>
                </a>

                <a href="{{ route('antrean.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('antrean.index') ? 'bg-emerald-800 text-white shadow-inner' : 'hover:bg-emerald-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Presensi & Antrean</span>
                </a>

                <a href="{{ route('berkas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-emerald-800/50 hover:text-white transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Berkas & Putusan</span>
                </a>
                @endcan

                <a href="{{ route('kalkulator.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-emerald-800/50 hover:text-white transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Kalkulator e-SKUM</span>
                </a>

                @can('manage-perkara')
                <a href="{{ route('delegasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-emerald-800/50 hover:text-white transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Delegasi Perkara</span>
                </a>
                @endcan

                <a href="{{ route('relaas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-emerald-800/50 hover:text-white transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Monitoring Relaas</span>
                </a>

@can('manage-hakim')
                <a href="{{ route('laporan.statistik') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-emerald-800/50 hover:text-white transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Laporan & Statistik</span>
                </a>
                @endcan

            </nav>

            <!-- Footer Sidebar -->
            <div class="p-4 border-t border-emerald-800 bg-emerald-950">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-700 flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div x-show="sidebarOpen" class="overflow-hidden">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-emerald-400 capitalize">{{ Auth::user()->role ?? 'Masyarakat' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
@csrf

                    
                    
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
                <form method="POST" action="{{ route('logout') }}" x-show="!sidebarOpen">
@csrf

                    
                    
                    <button type="submit" class="w-full flex items-center justify-center p-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0" :class="highContrast ? 'bg-black border-l border-gray-700' : 'bg-gray-50'">
            <!-- Topbar -->
            <header class="h-16 border-b flex items-center justify-between px-4 sm:px-6 z-10 shadow-sm transition-colors" :class="highContrast ? 'bg-gray-900 border-gray-700' : 'bg-white border-gray-200'">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500" :class="highContrast ? 'text-gray-300 hover:bg-gray-800' : 'text-gray-500 hover:bg-gray-100'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    </button>
                    <form action="{{ route('dashboard') }}" method="GET" class="hidden sm:block relative focus-within:text-emerald-500" :class="highContrast ? 'text-gray-400' : 'text-gray-400'">
                        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari perkara..." class="pl-10 pr-4 py-2 border rounded-full text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 w-64 transition-all" :class="highContrast ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-500' : 'border-gray-300 bg-white text-gray-900'">
                    </form>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Accessibility: Text Size -->
                    <div class="flex items-center bg-gray-100 rounded-lg p-1" :class="highContrast ? 'bg-gray-800' : 'bg-gray-100'">
                        <button @click="textSize = 'normal'" class="px-2 py-1 text-xs font-bold rounded" :class="textSize === 'normal' ? 'bg-white shadow text-emerald-600' : 'text-gray-500'">A</button>
                        <button @click="textSize = 'large'" class="px-2 py-1 text-sm font-bold rounded" :class="textSize === 'large' ? 'bg-white shadow text-emerald-600' : 'text-gray-500'">A</button>
                        <button @click="textSize = 'xlarge'" class="px-2 py-1 text-base font-bold rounded" :class="textSize === 'xlarge' ? 'bg-white shadow text-emerald-600' : 'text-gray-500'">A</button>
                    </div>
                    <!-- Accessibility: High Contrast Toggle -->
                    <button @click="highContrast = !highContrast" class="p-2 rounded-lg transition-colors focus:outline-none" :class="highContrast ? 'text-yellow-400 hover:bg-gray-800' : 'text-gray-500 hover:bg-gray-100'" title="Mode Kontras Tinggi">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                    <!-- Notifications -->
                    <button class="relative p-2 ml-2 transition-colors" :class="highContrast ? 'text-gray-300 hover:text-emerald-400' : 'text-gray-400 hover:text-emerald-600'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-4 sm:p-6 custom-scrollbar" :class="highContrast ? 'text-gray-200' : ''">
                @yield('content')
            </div>
        </main>
    </div>

    <style>
        /* Custom Scrollbar for a premium feel */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 10px;
        }
        aside.custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.8);
        }

        /* Print optimization styles for clean PDF export */
        @media print {
            html, body {
                background: white !important;
                color: black !important;
                font-size: 9pt !important;
                height: 100% !important;
                overflow: hidden !important;
            }
            .flex.h-screen.overflow-hidden {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
            }
            .flex-1.overflow-auto {
                overflow: visible !important;
                height: auto !important;
            }
            aside, header, nav, button, form, svg, .print\:hidden {
                display: none !important;
            }
            main {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
                height: auto !important;
                display: block !important;
            }
            .grid {
                display: grid !important;
            }
            .grid-cols-4 {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 0.5rem !important;
            }
            .flex {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            .mb-6 {
                margin-bottom: 0.4rem !important;
            }
            .mt-4, .mt-2 {
                margin-top: 0.1rem !important;
            }
            /* Style print-friendly cards */
            .bg-white, .bg-emerald-600 {
                background-color: transparent !important;
                color: black !important;
                border: 1px solid #e5e7eb !important;
                box-shadow: none !important;
                margin-bottom: 0.4rem !important;
                padding: 0.4rem !important;
                page-break-inside: avoid !important;
            }
            .bg-white *, .bg-emerald-600 * {
                color: black !important;
            }
            .text-3xl, .text-4xl {
                font-size: 1.3rem !important;
                font-weight: 800 !important;
            }
            .text-emerald-700, .text-emerald-600, .text-blue-600, .text-amber-700 {
                color: black !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 0.2rem !important;
            }
            th, td {
                border: 1px solid #d1d5db !important;
                padding: 3px 6px !important;
                font-size: 8.5pt !important;
            }
            h1, h2 {
                font-size: 12pt !important;
                margin-bottom: 0.1rem !important;
                font-weight: 700 !important;
            }
            p {
                font-size: 8.5pt !important;
                margin-bottom: 0.1rem !important;
            }
            /* Hide print margins for browser */
            @page {
                margin: 0.6cm !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
    
    @stack('modals')
    @stack('scripts')
</body>
</html>
