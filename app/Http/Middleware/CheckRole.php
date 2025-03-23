<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Debug
        Log::debug('CheckRole middleware triggered with role: ' . $role);
        Log::debug('User roles: ' . ($request->user() ? implode(', ', $request->user()->getRoleNames()->toArray()) : 'No user'));

        if (!$request->user() || !$request->user()->hasRole($role)) {
            abort(403, 'Unauthorized action. You do not have the required permissions to access this page.');
        }

        return $next($request);
    }
}
