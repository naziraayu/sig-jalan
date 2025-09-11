<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     * @param  string  $action
     * @param  string|null  $feature
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $action, ?string $feature = null): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();


        if (!$user || !$user->hasPermission($action, $feature)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
