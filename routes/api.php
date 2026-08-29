<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiDashboardController;
use App\Http\Controllers\Api\ApiPerkaraController;
use App\Http\Controllers\Api\ApiSippController;
use App\Http\Controllers\Api\ApiJadwalSidangController;
use App\Http\Controllers\Api\ApiKehadiranHakimController;
use App\Http\Controllers\Api\ApiAntreanController;
use App\Http\Controllers\Api\ApiBerkasPerkaraController;
use App\Http\Controllers\Api\ApiKalkulatorController;
use App\Http\Controllers\Api\ApiDelegasiController;
use App\Http\Controllers\Api\ApiRelaasController;
use App\Http\Controllers\Api\ApiLaporanStatistikController;
use App\Http\Controllers\Api\ApiUserController;

Route::name('api.')->group(function () {
    // Authentication (Public)
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/register', [ApiAuthController::class, 'register']);

    // Public Antrean Check-in
    Route::post('/checkin', [ApiAntreanController::class, 'store']);

    Route::middleware([\App\Http\Middleware\BypassAuth::class])->group(function () {
        // Auth related
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/me', [ApiAuthController::class, 'me']);
        
        // Semua Role: Admin, Hakim, Masyarakat
        Route::get('/dashboard', [ApiDashboardController::class, 'index']);
        Route::get('/kalkulator-biaya', [ApiKalkulatorController::class, 'index']);
        Route::get('/relaas-panggilan', [ApiRelaasController::class, 'index']);
        Route::put('/relaas-panggilan/{id}', [ApiRelaasController::class, 'updateStatus']);

        // Registrasi Perkara Mandiri (Masyarakat)
        Route::get('/perkara-saya', [ApiPerkaraController::class, 'index']);
        Route::post('/registrasi-perkara', [ApiPerkaraController::class, 'storeMandiri']);
        
        // Admin Verifikasi Perkara
        Route::get('/admin/verifikasi-perkara', [ApiPerkaraController::class, 'adminIndex']);
        Route::post('/admin/verifikasi-perkara/{id}/bayar', [ApiPerkaraController::class, 'adminConfirmPembayaran']);
        Route::post('/admin/verifikasi-perkara/{id}/verifikasi', [ApiPerkaraController::class, 'adminVerify']);

        // Hakim Jadwal & Putusan
        Route::get('/hakim/jadwal-sidang', [ApiPerkaraController::class, 'hakimIndex']);
        Route::post('/hakim/jadwal-sidang/{id}/putusan', [ApiPerkaraController::class, 'hakimPutusan']);
        
        // e-Berpadu & e-Raterang
        Route::apiResource('e-berpadu', \App\Http\Controllers\Api\ApiEBerpaduController::class)->except(['destroy']);
        Route::apiResource('e-raterang', \App\Http\Controllers\Api\ApiERaterangController::class)->except(['destroy']);

        // Admin & Hakim (Hakim only gets these specific routes, Admin gets everything via middleware)
        Route::middleware(['role:hakim,admin'])->group(function () {
            // Jadwal Sidang
            Route::apiResource('jadwal-sidang', ApiJadwalSidangController::class);
            Route::post('/jadwal-sidang/{id}/panggil', [ApiJadwalSidangController::class, 'panggil']);
            
            // Kehadiran Hakim
            Route::apiResource('kehadiran', ApiKehadiranHakimController::class);
            Route::get('/antrean-sidang', [ApiAntreanController::class, 'index']);
            
            // Berkas & Draf Putusan
            Route::apiResource('berkas-perkara', ApiBerkasPerkaraController::class);
            Route::get('/berkas-perkara/{id}/anonim', [ApiBerkasPerkaraController::class, 'downloadAnonim']);
            
            // Laporan & Statistik
            Route::get('/laporan-statistik', [ApiLaporanStatistikController::class, 'index']);
        });

        // Hanya Admin
        Route::middleware(['role:admin'])->group(function () {
            // User Management
            Route::apiResource('users', ApiUserController::class);

            // Perdata & Pidana (SIPP)
            Route::get('/perdata-umum', [ApiSippController::class, 'perdataUmum']);
            Route::post('/perdata-umum', [ApiSippController::class, 'storePerdata']);
            Route::put('/perdata-umum/{id}', [ApiSippController::class, 'updatePerdata']);
            Route::delete('/perdata-umum/{id}', [ApiSippController::class, 'destroyPerdata']);
            
            // Perdata Khusus
            Route::get('/perdata-khusus', [ApiSippController::class, 'perdataKhusus']);
            Route::post('/perdata-khusus', [ApiSippController::class, 'storePerdata']);
            Route::put('/perdata-khusus/{id}', [ApiSippController::class, 'updatePerdata']);
            Route::delete('/perdata-khusus/{id}', [ApiSippController::class, 'destroyPerdata']);
            
            Route::get('/pidana', [ApiSippController::class, 'pidanaBiasa']);
            Route::post('/pidana', [ApiSippController::class, 'storePidana']);
            Route::put('/pidana/{id}', [ApiSippController::class, 'updatePidana']);
            Route::delete('/pidana/{id}', [ApiSippController::class, 'destroyPidana']);
            
            Route::get('/pidana-khusus', [ApiSippController::class, 'pidanaKhusus']);

            // Delegasi
            Route::apiResource('delegasi', ApiDelegasiController::class);
        });
    });
});
