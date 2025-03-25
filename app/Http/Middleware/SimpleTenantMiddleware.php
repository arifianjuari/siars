<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SimpleTenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->tenant_id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke tenant manapun. Hubungi administrator.');
        }

        return $next($request);
    }
}
