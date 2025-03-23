<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $isTenantAdmin = $user->hasRole('TenantAdmin');

        // Dapatkan semua modul yang aktif
        $modules = Module::where('is_active', true)->get();

        // Jika pengguna adalah tenant admin dan memiliki tenant_id
        if ($user->tenant_id) {
            // Dapatkan ID modul yang sudah aktif untuk tenant
            $activeModuleIds = DB::table('tenant_modules')
                ->where('tenant_id', $user->tenant_id)
                ->where('is_active', true)
                ->pluck('module_id')
                ->toArray();

            // Dapatkan ID modul yang sedang diproses permintaan aktivasinya
            $pendingModuleIds = DB::table('module_activation_requests')
                ->where('tenant_id', $user->tenant_id)
                ->where('status', 'pending')
                ->pluck('module_id')
                ->toArray();

            // Tandai masing-masing modul apakah sudah aktif untuk tenant pengguna
            $modules->each(function ($module) use ($activeModuleIds, $pendingModuleIds) {
                $module->is_active_for_tenant = in_array($module->id, $activeModuleIds);
                $module->is_pending_activation = in_array($module->id, $pendingModuleIds);
            });
        }

        return view('modules.index', compact('modules', 'isTenantAdmin'));
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
