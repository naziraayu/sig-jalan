<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles)
{
    $user = $request->user();

    // superadmin = akses semua
    if ($user && $user->role()) {
        return $next($request);
    }

    // parse role parameter jadi array
    $roles = explode(',', $roles);

    // cek berdasarkan role relasi
    if ($user && $user->role && in_array($user->role->name, $roles)) {
        return $next($request);
    }

    abort(403, 'Unauthorized');
}

}
