<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BypassAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Paksa login sebagai user pertama (biasanya Super Admin) tanpa perlu token
        if (!Auth::check()) {
            $user = User::first();
            if ($user) {
                Auth::login($user);
            }
        }
        return $next($request);
    }
}
