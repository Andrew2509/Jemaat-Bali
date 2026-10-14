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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan masuk terlebih dahulu.');
        }

        if (! $user->is_active) {
            abort(403, 'Akun Anda sedang dinonaktifkan.');
        }

        if (! $user->hasRole($roles) && ! $user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki peran yang diizinkan untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
