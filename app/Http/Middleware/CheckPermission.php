<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan masuk terlebih dahulu.');
        }

        if (! $user->is_active) {
            abort(403, 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator.');
        }

        if (! $user->hasPermission($permission)) {
            abort(403, 'Anda tidak memiliki hak akses (izin: ' . $permission . ') untuk halaman atau aksi ini.');
        }

        return $next($request);
    }
}
