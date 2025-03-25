<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ModuleActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $moduleCode  Kode modul yang akan divalidasi
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        try {
            // Dapatkan tenant aktif
            $tenantSelectionService = App::make('App\Services\TenantSelectionService');
            $tenant = $tenantSelectionService->getActiveTenant();

            if (!$tenant) {
                return redirect()->route('dashboard')
                    ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
            }

            // Dapatkan modul berdasarkan kode
            $module = \App\Models\Module::where('code', $moduleCode)->first();

            if (!$module) {
                return redirect()->route('dashboard')
                    ->withErrors(['module' => "Modul {$moduleCode} tidak ditemukan"]);
            }

            // Periksa apakah modul aktif dan sudah diapprove untuk tenant
            $isActive = \App\Models\TenantModule::where('module_id', $module->id)
                ->where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at')
                ->exists();

            if (!$isActive) {
                return redirect()->route('dashboard')
                    ->withErrors(['module' => "Modul {$module->name} tidak aktif atau belum disetujui untuk rumah sakit ini"]);
            }

            // Tambahkan info modul ke request
            $request->merge(['active_module' => $module]);

            return $next($request);
        } catch (\Exception $e) {
            // Tangani kesalahan dengan mengarahkan kembali ke dashboard
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Terjadi kesalahan saat memverifikasi akses modul: ' . $e->getMessage()]);
        }
    }
}
