<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckTenantOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Pastikan user terautentikasi
        if (!$user) {
            return redirect()->route('login');
        }

        // Jika user adalah superadmin, allow access ke semua tenant
        if ($user->hasRole('Superadmin')) {
            return $next($request);
        }

        // Ambil ID tenant dari parameter request atau default ke tenant pengguna
        $tenantId = $request->route('tenant_id') ?? $request->input('tenant_id');

        // Jika tidak ada tenant ID yang diminta, gunakan tenant pengguna saat ini
        if (!$tenantId) {
            // Jika user adalah TenantAdmin tapi tidak ada tenant_id, pastikan user memiliki tenant
            if ($user->hasRole('TenantAdmin') && !$user->tenant_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak terkait dengan rumah sakit manapun.');
            }

            return $next($request);
        }

        // Jika user adalah TenantAdmin, hanya bisa akses tenant mereka sendiri
        if ($user->hasRole('TenantAdmin') && $user->tenant_id != $tenantId) {
            return redirect()->route('tenantadmin.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke rumah sakit yang diminta.');
        }

        // Untuk user biasa, hanya bisa akses tenant mereka sendiri
        if ($user->tenant_id != $tenantId) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke rumah sakit yang diminta.');
        }

        return $next($request);
    }
}
