<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    /**
     * Service untuk pengelolaan modul
     *
     * @var ModuleService
     */
    protected $moduleService;

    /**
     * Konstruktor
     *
     * @param ModuleService $moduleService
     */
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
        $this->middleware(['auth']);
    }

    /**
     * Menampilkan daftar modul yang tersedia
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $modules = $this->moduleService->getAllModules();

        // Filter modul yang dapat dilihat oleh user (superadmin dapat melihat semua)
        if (!$user->isSuperadmin()) {
            $modules = $modules->filter(function ($module) use ($user) {
                return $user->canViewModule($module);
            });
        }

        // Tambahkan informasi status modul untuk tenant
        if ($user->tenant_id) {
            $modules = $modules->map(function ($module) use ($user) {
                $isActive = $this->moduleService->isModuleActiveForTenant($module->id, $user->tenant_id);
                $module->is_active_for_tenant = $isActive;
                return $module;
            });
        }

        return view('modules.index', [
            'modules' => $modules,
            'isTenantAdmin' => $user->hasRole('TenantAdmin')
        ]);
    }

    /**
     * Menampilkan detail modul
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        $module = Module::findOrFail($id);

        // Validasi apakah user dapat melihat modul (kecuali superadmin)
        if (!$user->isSuperadmin() && !$user->canViewModule($module)) {
            abort(403, 'Anda tidak memiliki izin untuk melihat modul ini');
        }

        $tenantHasModule = false;
        $isModuleActive = false;

        // Cek apakah user terkait dengan tenant dan apakah tenant memiliki modul ini
        if ($user->tenant_id) {
            $tenantHasModule = true;
            $isModuleActive = $this->moduleService->isModuleActiveForTenant($module->id, $user->tenant_id);
        }

        return view('modules.show', [
            'module' => $module,
            'tenantHasModule' => $tenantHasModule,
            'isModuleActive' => $isModuleActive,
        ]);
    }
}
