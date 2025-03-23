<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleActivationRequest;
use App\Models\Tenant;
use App\Services\ModuleActivationService;
use App\Services\ModuleService;
use App\Services\TenantSelectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperadminModuleController extends Controller
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
     * Tampilkan daftar modul untuk tenant yang dipilih
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();

        if (!$tenant) {
            return redirect()->route('superadmin.tenants.select')
                ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
        }

        $modules = $this->moduleService->getModulesByTenant($tenant);

        return view('superadmin.modules.index', [
            'tenant' => $tenant,
            'modules' => $modules
        ]);
    }

    /**
     * Menampilkan daftar permintaan aktivasi modul untuk seluruh tenant atau tenant tertentu
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function requests(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $status = $request->input('status', 'pending');

        $requests = ModuleActivationRequest::with(['tenant', 'module', 'requestedBy'])
            ->when($tenantId, function ($query) use ($tenantId) {
                return $query->where('tenant_id', $tenantId);
            })
            ->where('status', $status)
            ->orderBy('requested_at', 'desc')
            ->paginate(10);

        $tenants = Tenant::orderBy('name')->get();

        return view('superadmin.modules.requests', [
            'requests' => $requests,
            'tenants' => $tenants,
            'selectedTenant' => $tenantId,
            'selectedStatus' => $status
        ]);
    }

    /**
     * Mengubah status aktivasi modul untuk tenant tertentu
     *
     * @param Request $request
     * @param int $moduleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Request $request, int $moduleId)
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();

        if (!$tenant) {
            return redirect()->back()
                ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
        }

        $user = Auth::user();
        $module = Module::findOrFail($moduleId);

        // Validasi izin untuk aktivasi
        if (!$user->canActivateModule($module)) {
            return redirect()->back()
                ->withErrors(['permission' => 'Anda tidak memiliki izin untuk mengaktifkan modul ini']);
        }

        try {
            $isActive = $this->moduleService->isModuleActiveForTenant($moduleId, $tenant->id);

            if ($isActive) {
                $this->moduleActivationService->deactivateModule($moduleId, $tenant->id);
                $message = "Modul {$module->name} berhasil dinonaktifkan";
            } else {
                $this->moduleActivationService->activateModule($moduleId, $tenant->id, $user->id);
                $message = "Modul {$module->name} berhasil diaktifkan";
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengubah status modul: ' . $e->getMessage()]);
        }
    }

    /**
     * Memproses permintaan aktivasi modul (approve/reject)
     *
     * @param Request $request
     * @param int $requestId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processRequest(Request $request, int $requestId)
    {
        $action = $request->input('action');
        $notes = $request->input('notes');

        if (!in_array($action, ['approve', 'reject'])) {
            return redirect()->back()
                ->withErrors(['action' => 'Tindakan tidak valid']);
        }

        $user = Auth::user();
        $activationRequest = ModuleActivationRequest::findOrFail($requestId);

        // Validasi status permintaan
        if ($activationRequest->status !== 'pending') {
            return redirect()->back()
                ->withErrors(['status' => 'Permintaan ini sudah diproses sebelumnya']);
        }

        // Validasi izin untuk memproses permintaan
        if (!$user->canActivateModule($activationRequest->module)) {
            return redirect()->back()
                ->withErrors(['permission' => 'Anda tidak memiliki izin untuk memproses permintaan ini']);
        }

        try {
            $status = $action === 'approve' ? 'approved' : 'rejected';

            $this->moduleActivationService->processActivationRequest(
                $activationRequest,
                $user,
                $status,
                $notes
            );

            $message = $action === 'approve'
                ? "Permintaan aktivasi modul {$activationRequest->module->name} berhasil disetujui"
                : "Permintaan aktivasi modul {$activationRequest->module->name} berhasil ditolak";

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal memproses permintaan: ' . $e->getMessage()]);
        }
    }
}
