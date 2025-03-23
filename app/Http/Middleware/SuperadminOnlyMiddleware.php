<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperadminOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Cek apakah user adalah superadmin
        if (!$user->isSuperadmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.',
                    'status' => 'error'
                ], 403);
            }

            return redirect()->route('dashboard')
                ->withErrors(['access' => 'Anda tidak memiliki izin untuk mengakses fitur ini.']);
        }

        return $next($request);
    }
}
