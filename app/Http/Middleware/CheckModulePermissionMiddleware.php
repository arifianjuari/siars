<?php

namespace App\Http\Middleware;

use App\Services\ModuleService;
use App\Services\TenantSelectionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermissionMiddleware
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
     * @param  string  $action  Aksi yang akan dilakukan (view, create, edit, delete, approve, activate)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $moduleCode, string $action = 'view'): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

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

        // Periksa apakah modul aktif untuk tenant
        if (!$this->moduleService->isModuleActiveForTenant($module->id, $tenant->id)) {
            return redirect()->route('dashboard')
                ->withErrors(['module' => "Modul {$module->name} tidak aktif untuk rumah sakit ini"]);
        }

        // Validasi izin user
        $hasPermission = false;

        switch ($action) {
            case 'view':
                $hasPermission = $user->canViewModule($module);
                break;
            case 'create':
                $hasPermission = $user->canCreateInModule($module);
                break;
            case 'edit':
                $hasPermission = $user->canEditInModule($module);
                break;
            case 'delete':
                $hasPermission = $user->canDeleteInModule($module);
                break;
            case 'approve':
                $hasPermission = $user->canApproveInModule($module);
                break;
            case 'activate':
                $hasPermission = $user->canActivateModule($module);
                break;
            default:
                $hasPermission = false;
        }

        if (!$hasPermission) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Anda tidak memiliki izin untuk {$action} di modul {$module->name}.",
                    'status' => 'error'
                ], 403);
            }

            return redirect()->route('dashboard')
                ->withErrors(['access' => "Anda tidak memiliki izin untuk {$action} di modul {$module->name}."]);
        }

        // Tambahkan info modul ke request
        $request->merge(['active_module' => $module]);

        return $next($request);
    }
}
