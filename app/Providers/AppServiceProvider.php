<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\Hash::setRounds(10);

        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || app()->environment('production') || isset($_SERVER['VERCEL'])) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Gate::define('manage-users', function ($user) {
            return in_array(strtolower(trim($user->role ?? '')), ['super_admin', 'admin']);
        });

        \Illuminate\Support\Facades\Gate::define('manage-perkara', function ($user) {
            return in_array(strtolower(trim($user->role ?? '')), ['super_admin', 'admin', 'petugas_pendaftaran', 'petugas', 'panitera', 'hakim']);
        });

        \Illuminate\Support\Facades\Gate::define('manage-hakim', function ($user) {
            return in_array(strtolower(trim($user->role ?? '')), ['super_admin', 'admin', 'hakim']);
        });
    }
}
