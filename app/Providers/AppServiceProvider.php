<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paksa HTTPS jika berjalan di Vercel/Production
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        Gate::define('manage-users', function ($user) {
            return in_array(strtolower(trim($user->role ?? '')), ['super_admin', 'admin']);
        });

        Gate::define('manage-perkara', function ($user) {
            return in_array(strtolower(trim($user->role ?? '')), ['super_admin', 'admin', 'petugas_pendaftaran', 'petugas', 'panitera', 'hakim']);
        });

        Gate::define('manage-hakim', function ($user) {
            return in_array(strtolower(trim($user->role ?? '')), ['super_admin', 'admin', 'hakim']);
        });
    }
}
