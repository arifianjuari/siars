<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Tenant;
use App\Services\ModuleActivationService;
use App\Services\ModuleService;
use App\Services\TenantSelectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantModuleController extends Controller
{
    /**
     * Service untuk pengelolaan modul
     *
     * @var ModuleService
     */
    protected $moduleService;

    /**
     * Service untuk aktivasi modul
     *
     * @var ModuleActivationService
     */
    protected $moduleActivationService;

    /**
     * Service untuk pemilihan tenant
     *
     * @var TenantSelectionService
     */
    protected $tenantSelectionService;

    /**
     * Konstruktor
     *
     * @param ModuleService $moduleService
     * @param ModuleActivationService $moduleActivationService
     * @param TenantSelectionService $tenantSelectionService
     */
    public function __construct(
        ModuleService $moduleService,
        ModuleActivationService $moduleActivationService,
        TenantSelectionService $tenantSelectionService
    ) {
        $this->moduleService = $moduleService;
        $this->moduleActivationService = $moduleActivationService;
        $this->tenantSelectionService = $tenantSelectionService;
    }

    /**
     * Menampilkan modul untuk tenant tertentu
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = null;

        // Jika user adalah admin RS, gunakan tenant dari user
        if ($user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
        } else {
            // Jika user adalah superadmin, gunakan tenant yang dipilih
            $tenant = $this->tenantSelectionService->getActiveTenant();

            if (!$tenant) {
                return redirect()->route('superadmin.select-tenant')
                    ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
            }
        }

        $modules = $this->moduleService->getModulesByTenant($tenant);

        return view('tenant-modules.index', [
            'tenant' => $tenant,
            'modules' => $modules
        ]);
    }

    /**
     * Mengaktifkan modul untuk tenant (hanya superadmin)
     *
     * @param Request $request
     * @param int $moduleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(Request $request, $moduleId)
    {
        $user = Auth::user();

        // Validasi apakah user adalah superadmin
        if (!$user->hasRole('Superadmin')) {
            abort(403, 'Hanya superadmin yang dapat mengaktifkan modul');
        }

        $tenantId = $request->input('tenant_id');

        // Jika tenant_id tidak ada dalam request, gunakan tenant aktif
        if (!$tenantId) {
            $tenant = $this->tenantSelectionService->getActiveTenant();

            if (!$tenant) {
                return redirect()->back()
                    ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
            }

            $tenantId = $tenant->id;
        }

        // Validasi akses tenant
        if (!$this->tenantSelectionService->userCanAccessTenant($user->id, $tenantId)) {
            return redirect()->back()
                ->withErrors(['tenant' => 'Anda tidak memiliki akses ke rumah sakit ini']);
        }

        try {
            $settings = $request->input('settings', []);
            $this->moduleActivationService->activateModule($moduleId, $tenantId, $user->id, $settings);

            $module = Module::find($moduleId);
            return redirect()->back()
                ->with('success', "Modul {$module->name} berhasil diaktifkan");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengaktifkan modul: ' . $e->getMessage()]);
        }
    }

    /**
     * Menonaktifkan modul untuk tenant (hanya superadmin)
     *
     * @param Request $request
     * @param int $moduleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(Request $request, $moduleId)
    {
        $user = Auth::user();

        // Validasi apakah user adalah superadmin
        if (!$user->hasRole('Superadmin')) {
            abort(403, 'Hanya superadmin yang dapat menonaktifkan modul');
        }

        $tenantId = $request->input('tenant_id');

        // Jika tenant_id tidak ada dalam request, gunakan tenant aktif
        if (!$tenantId) {
            $tenant = $this->tenantSelectionService->getActiveTenant();

            if (!$tenant) {
                return redirect()->back()
                    ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
            }

            $tenantId = $tenant->id;
        }

        // Validasi akses tenant
        if (!$this->tenantSelectionService->userCanAccessTenant($user->id, $tenantId)) {
            return redirect()->back()
                ->withErrors(['tenant' => 'Anda tidak memiliki akses ke rumah sakit ini']);
        }

        try {
            $this->moduleActivationService->deactivateModule($moduleId, $tenantId);

            $module = Module::find($moduleId);
            return redirect()->back()
                ->with('success', "Modul {$module->name} berhasil dinonaktifkan");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menonaktifkan modul: ' . $e->getMessage()]);
        }
    }

    /**
     * Mengajukan permintaan aktivasi modul untuk admin RS
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestActivation(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'notes' => 'nullable|string'
        ]);

        $user = Auth::user();

        // Validasi apakah user terhubung dengan tenant
        if (!$user->tenant_id) {
            return redirect()->back()
                ->withErrors(['tenant' => 'Anda tidak terhubung dengan rumah sakit manapun']);
        }

        try {
            $moduleId = $validated['module_id'];
            $notes = $validated['notes'] ?? '';
            $tenantId = $user->tenant_id; // Pastikan tenant_id diteruskan sebagai string tanpa diubah

            $this->moduleActivationService->requestActivation(
                $moduleId,
                $tenantId,
                $user->id,
                $notes
            );

            $module = Module::find($moduleId);
            return redirect()->back()
                ->with('success', "Permintaan aktivasi modul {$module->name} berhasil dikirim dan menunggu persetujuan");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal membuat permintaan aktivasi: ' . $e->getMessage()]);
        }
    }
}
