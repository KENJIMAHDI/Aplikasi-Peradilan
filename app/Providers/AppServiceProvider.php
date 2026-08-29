<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;

class SafeBcryptHasher extends BcryptHasher
{
    public function make($value, array $options = [])
    {
        $cost = isset($options['rounds']) ? (int)$options['rounds'] : 10;
        if ($cost < 4 || $cost > 31) {
            $cost = 10;
        }

        try {
            return password_hash($value, PASSWORD_BCRYPT, ['cost' => $cost]);
        } catch (\Throwable $e) {
            return password_hash($value, PASSWORD_DEFAULT);
        }
    }

    public function needsRehash($hashedValue, array $options = [])
    {
        return false;
    }
}

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
        Hash::extend('bcrypt', function () {
            return new SafeBcryptHasher;
        });

        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || app()->environment('production') || isset($_SERVER['VERCEL'])) {
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
