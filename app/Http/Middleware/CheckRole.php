<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect('login');
        }

        $user = auth()->user();
        $userRole = strtolower(trim($user->role ?? ''));

        // super_admin dan admin selalu memiliki akses penuh ke semua modul
        if (in_array($userRole, ['super_admin', 'admin'])) {
            return $next($request);
        }

        $allowedRoles = array_map('strtolower', array_map('trim', $roles));
        if (!in_array($userRole, $allowedRoles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Akses ditolak: Anda tidak memiliki izin.'], 403);
            }
            abort(403, 'Akses ditolak: Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}
