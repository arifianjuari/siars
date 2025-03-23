<?php

namespace App\Http\Middleware;

use App\Services\TenantSelectionService;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantAccessMiddleware
{
    /**
     * Service untuk pengelolaan seleksi tenant
     *
     * @var TenantSelectionService
     */
    protected $tenantSelectionService;

    /**
     * Konstruktor
     *
     * @param TenantSelectionService $tenantSelectionService
     */
    public function __construct(TenantSelectionService $tenantSelectionService)
    {
        $this->tenantSelectionService = $tenantSelectionService;
    }

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

        // Pastikan user sudah login
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            // Coba dapatkan tenant aktif
            $tenant = $this->tenantSelectionService->getActiveTenant($user);

            // Jika tidak ada tenant aktif
            if (!$tenant) {
                // Untuk superadmin, arahkan ke halaman pilih tenant
                if ($user->isSuperadmin()) {
                    return redirect()->route('superadmin.select-tenant');
                }

                // Untuk user biasa, tampilkan error
                throw new AuthorizationException('Anda tidak terhubung dengan rumah sakit manapun.');
            }

            // Tetapkan tenant ke request untuk digunakan di controller
            $request->merge(['active_tenant' => $tenant]);

            return $next($request);
        } catch (AuthorizationException $e) {
            // Tampilkan pesan error
            return redirect()->route('dashboard')->withErrors(['tenant' => $e->getMessage()]);
        }
    }
}
