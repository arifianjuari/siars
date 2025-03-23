<?php

namespace App\Http\Middleware;

use App\Services\ModuleService;
use App\Services\TenantSelectionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleActiveMiddleware
{
    /**
     * Service untuk pengelolaan modul
     *
     * @var ModuleService
     */
    protected $moduleService;

    /**
     * Service untuk pengelolaan tenant
     *
     * @var TenantSelectionService
     */
    protected $tenantSelectionService;

    /**
     * Konstruktor
     *
     * @param ModuleService $moduleService
     * @param TenantSelectionService $tenantSelectionService
     */
    public function __construct(
        ModuleService $moduleService,
        TenantSelectionService $tenantSelectionService
    ) {
        $this->moduleService = $moduleService;
        $this->tenantSelectionService = $tenantSelectionService;
    }

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
        $tenant = $this->tenantSelectionService->getActiveTenant();

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
    }
}
