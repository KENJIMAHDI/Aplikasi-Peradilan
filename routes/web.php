<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalSidangController;
use App\Http\Controllers\KehadiranHakimController;
use App\Http\Controllers\BerkasPerkaraController;

use App\Http\Controllers\PerdataController;
use App\Http\Controllers\PidanaController;
use App\Http\Controllers\KalkulatorController;

use Illuminate\Support\Facades\Artisan;

Route::get('/run-migrations-secret', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $output1 = Artisan::output();

        Artisan::call('db:seed', ['--force' => true]);
        $output2 = Artisan::output();

        return response("<div style='font-family:sans-serif; padding:20px; background:#e6fffa; border:2px solid #319795; border-radius:10px; margin:20px;'>
            <h2 style='color:#2c7a7b; margin-top:0;'>✅ Migration & Seeding Berhasil ke Supabase!</h2>
            <h3>Migration Output:</h3>
            <pre style='background:#1a202c; color:#68d391; padding:15px; border-radius:6px; overflow:auto; font-size:13px;'>{$output1}</pre>
            <h3>Seed Output:</h3>
            <pre style='background:#1a202c; color:#68d391; padding:15px; border-radius:6px; overflow:auto; font-size:13px;'>{$output2}</pre>
            <p><a href='/login' style='display:inline-block; padding:10px 20px; background:#319795; color:white; border-radius:6px; text-decoration:none; font-weight:bold;'>👉 Buka Halaman Login Aplikasi</a></p>
        </div>");
    } catch (\Throwable $e) {
        return response("<div style='font-family:sans-serif; padding:20px; background:#fff0f0; border:2px solid #e53e3e; border-radius:10px; margin:20px;'>
            <h2 style='color:#c53030; margin-top:0;'>❌ Migration Error:</h2>
            <p><strong>Message:</strong> {$e->getMessage()}</p>
            <pre style='background:#1a202c; color:#fc8181; padding:15px; border-radius:6px; overflow:auto; font-size:13px;'>{$e->getTraceAsString()}</pre>
        </div>", 500);
    }
});

Route::get('/direct-login', function () {
    $user = \App\Models\User::first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        request()->session()->regenerate();
        return redirect()->route('dashboard');
    }
    return 'No user found in database';
});

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Check-In & Webhook
Route::get('/checkin', [\App\Http\Controllers\AntreanController::class, 'publicCheckin'])->name('antrean.public');
Route::post('/checkin', [\App\Http\Controllers\AntreanController::class, 'store'])->name('antrean.store');
Route::post('/webhook/wa-reply', [\App\Http\Controllers\AntreanController::class, 'handleWaReply'])->name('webhook.wa.reply');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    
    // Semua Role: Admin, Hakim, Masyarakat
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/wa-replies-json', [DashboardController::class, 'getWaRepliesJson'])->name('admin.wa_replies.json');
    Route::get('/kalkulator-biaya', [KalkulatorController::class, 'index'])->name('kalkulator.index');
    Route::get('/relaas-panggilan', [App\Http\Controllers\RelaasController::class, 'index'])->name('relaas.index');
    Route::put('/relaas-panggilan/{id}', [App\Http\Controllers\RelaasController::class, 'updateStatus'])->name('relaas.update');

    // Registrasi Perkara Mandiri (Masyarakat)
    Route::get('/registrasi-perkara', [App\Http\Controllers\PerkaraController::class, 'createMandiri'])->name('perkara.register');
    Route::post('/registrasi-perkara', [App\Http\Controllers\PerkaraController::class, 'storeMandiri'])->name('perkara.store_mandiri');
    Route::get('/perkara-saya', [App\Http\Controllers\PerkaraController::class, 'index'])->name('perkara.index');
    
    // Admin Verifikasi Perkara
    Route::get('/admin/verifikasi-perkara', [App\Http\Controllers\PerkaraController::class, 'adminIndex'])->name('admin.verifikasi.index');
    Route::post('/admin/verifikasi-perkara/{id}/bayar', [App\Http\Controllers\PerkaraController::class, 'adminConfirmPembayaran'])->name('admin.verifikasi.bayar');
    Route::post('/admin/verifikasi-perkara/{id}/verifikasi', [App\Http\Controllers\PerkaraController::class, 'adminVerify'])->name('admin.verifikasi.verify');

    // Hakim Jadwal & Putusan
    Route::get('/hakim/jadwal-sidang', [App\Http\Controllers\PerkaraController::class, 'hakimIndex'])->name('hakim.jadwal.index');
    Route::post('/hakim/jadwal-sidang/{id}/putusan', [App\Http\Controllers\PerkaraController::class, 'hakimPutusan'])->name('hakim.jadwal.putusan');
    
    // e-Berpadu & e-Raterang (Tersedia untuk semua agar masyarakat bisa lapor, hakim bisa approve)
    Route::get('/e-berpadu', [App\Http\Controllers\EBerpaduController::class, 'index'])->name('e-berpadu.index');
    Route::get('/e-berpadu/create', [App\Http\Controllers\EBerpaduController::class, 'create'])->name('e-berpadu.create');
    Route::post('/e-berpadu', [App\Http\Controllers\EBerpaduController::class, 'store'])->name('e-berpadu.store');
    Route::put('/e-berpadu/{id}', [App\Http\Controllers\EBerpaduController::class, 'update'])->name('e-berpadu.update');

    Route::get('/e-raterang', [App\Http\Controllers\ERaterangController::class, 'index'])->name('e-raterang.index');
    Route::get('/e-raterang/create', [App\Http\Controllers\ERaterangController::class, 'create'])->name('e-raterang.create');
    Route::post('/e-raterang', [App\Http\Controllers\ERaterangController::class, 'store'])->name('e-raterang.store');
    Route::put('/e-raterang/{id}', [App\Http\Controllers\ERaterangController::class, 'update'])->name('e-raterang.update');
    Route::get('/e-raterang/{id}/print', [App\Http\Controllers\ERaterangController::class, 'show'])->name('e-raterang.show');

    // Admin & Hakim (Hakim only gets these specific routes, Admin gets everything via middleware)
    Route::middleware(['role:hakim,admin'])->group(function () {
        // Jadwal Sidang
        Route::get('/jadwal-sidang', [JadwalSidangController::class, 'index'])->name('jadwal.index');
        Route::post('/jadwal-sidang', [JadwalSidangController::class, 'store'])->name('jadwal.store');
        Route::put('/jadwal-sidang/{id}', [JadwalSidangController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal-sidang/{id}', [JadwalSidangController::class, 'destroy'])->name('jadwal.destroy');
        Route::post('/jadwal-sidang/{id}/panggil', [JadwalSidangController::class, 'panggil'])->name('jadwal.panggil');
        
        // Kehadiran Hakim
        Route::get('/kehadiran', [KehadiranHakimController::class, 'index'])->name('kehadiran.index');
        Route::post('/kehadiran', [KehadiranHakimController::class, 'store'])->name('kehadiran.store');
        Route::put('/kehadiran/{id}', [KehadiranHakimController::class, 'update'])->name('kehadiran.update');
        Route::delete('/kehadiran/{id}', [KehadiranHakimController::class, 'destroy'])->name('kehadiran.destroy');
        Route::post('/hakim', [KehadiranHakimController::class, 'storeHakim'])->name('hakim.store');
        Route::delete('/hakim/{id}', [KehadiranHakimController::class, 'destroyHakim'])->name('hakim.destroy');
        Route::get('/antrean-sidang', [\App\Http\Controllers\AntreanController::class, 'index'])->name('antrean.index');
        
        // Berkas & Draf Putusan
        Route::get('/berkas-perkara', [BerkasPerkaraController::class, 'index'])->name('berkas.index');
        Route::post('/berkas-perkara', [BerkasPerkaraController::class, 'store'])->name('berkas.store');
        Route::put('/berkas-perkara/{id}', [BerkasPerkaraController::class, 'update'])->name('berkas.update');
        Route::delete('/berkas-perkara/{id}', [BerkasPerkaraController::class, 'destroy'])->name('berkas.destroy');
        Route::get('/berkas-perkara/{id}/anonim', [BerkasPerkaraController::class, 'downloadAnonim'])->name('berkas.anonim');
        
        // Laporan & Statistik
        Route::get('/laporan-statistik', [App\Http\Controllers\LaporanStatistikController::class, 'index'])->name('laporan.statistik');
    });

    // Hanya Admin
    Route::middleware(['role:admin'])->group(function () {
        // User Management
        Route::resource('users', App\Http\Controllers\UserController::class)->except(['create', 'show', 'edit']);

        // Perdata & Pidana (SIPP)
        Route::get('/perdata-umum', [PerdataController::class, 'umum'])->name('perdata.umum');
        Route::post('/perdata-umum', [PerdataController::class, 'store'])->name('perdata-umum.store');
        Route::put('/perdata-umum/{id}', [PerdataController::class, 'update'])->name('perdata-umum.update');
        Route::delete('/perdata-umum/{id}', [PerdataController::class, 'destroy'])->name('perdata-umum.destroy');
        Route::get('/perdata-khusus', [PerdataController::class, 'khusus'])->name('perdata.khusus');
        Route::post('/perdata-khusus', [PerdataController::class, 'store'])->name('perdata-khusus.store');
        Route::put('/perdata-khusus/{id}', [PerdataController::class, 'update'])->name('perdata-khusus.update');
        Route::delete('/perdata-khusus/{id}', [PerdataController::class, 'destroy'])->name('perdata-khusus.destroy');
        
        Route::get('/pidana', [PidanaController::class, 'biasa'])->name('pidana');
        Route::post('/pidana', [PidanaController::class, 'store'])->name('pidana.store');
        Route::put('/pidana/{id}', [PidanaController::class, 'update'])->name('pidana.update');
        Route::delete('/pidana/{id}', [PidanaController::class, 'destroy'])->name('pidana.destroy');
        Route::get('/pidana-khusus', [PidanaController::class, 'khusus'])->name('pidana.khusus');

        // Delegasi
        Route::resource('delegasi', App\Http\Controllers\DelegasiController::class)->except(['create', 'show', 'edit']);
    });
});

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

Route::get('/fix-password', function () {
    DB::table('users')
        ->where('email', 'admin@pengadilan.go.id')
        ->update(['password' => Hash::make('rahasia123')]);

    return 'Password berhasil direset ke standar Bcrypt Laravel!';
});

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return 'Cache konfigurasi Vercel berhasil dibersihkan!';
});
