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
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\CheckRole;

class SuperadminController extends Controller
{
    protected $moduleService;
    protected $moduleActivationService;
    protected $tenantSelectionService;

    /**
     * Konstruktor
     */
    public function __construct(
        ModuleService $moduleService,
        ModuleActivationService $moduleActivationService,
        TenantSelectionService $tenantSelectionService
    ) {
        $this->middleware('auth');
        $this->middleware(CheckRole::class . ':Superadmin');

        $this->moduleService = $moduleService;
        $this->moduleActivationService = $moduleActivationService;
        $this->tenantSelectionService = $tenantSelectionService;
    }

    /**
     * Halaman daftar tenant yang dapat diakses
     */
    public function selectTenant()
    {
        $user = Auth::user();
        // Ambil tenant yang dapat diakses dan aktif
        $tenants = $user->accessibleTenants
            ->where('is_active', true);

        return view('superadmin.select-tenant', compact('tenants'));
    }

    /**
     * Menetapkan tenant aktif
     */
    public function setActiveTenant(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|string|exists:tenants,id'
        ]);

        try {
            $tenant = $this->tenantSelectionService->setActiveTenant($request->tenant_id);
            return redirect()->route('superadmin.dashboard')->with('success', "Berhasil beralih ke {$tenant->name}");
        } catch (\Exception $e) {
            Log::error('Gagal mengatur tenant aktif: ' . $e->getMessage());
            return back()->withErrors(['tenant' => 'Gagal beralih tenant: ' . $e->getMessage()]);
        }
    }

    /**
     * Dashboard superadmin
     */
    public function dashboard()
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();

        if (!$tenant) {
            return redirect()->route('superadmin.select-tenant')
                ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
        }

        return view('dashboard', compact('tenant'));
    }

    /**
     * Halaman daftar modul
     */
    public function moduleList()
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();

        if (!$tenant) {
            return redirect()->route('superadmin.select-tenant')
                ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
        }

        $modules = $this->moduleService->getModulesByTenant($tenant);

        return view('superadmin.modules.index', compact('tenant', 'modules'));
    }

    /**
     * Halaman daftar permintaan aktivasi modul
     */
    public function moduleRequests()
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();

        if (!$tenant) {
            return redirect()->route('superadmin.select-tenant')
                ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
        }

        $requests = ModuleActivationRequest::where('tenant_id', $tenant->id)
            ->with(['module', 'requestedBy', 'processedBy'])
            ->orderBy('requested_at', 'desc')
            ->paginate(10);

        return view('superadmin.modules.requests', compact('tenant', 'requests'));
    }

    /**
     * Toggle status aktivasi modul
     */
    public function toggleModuleStatus(Request $request, $moduleId)
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();

        if (!$tenant) {
            return redirect()->route('superadmin.select-tenant')
                ->withErrors(['tenant' => 'Silakan pilih rumah sakit terlebih dahulu']);
        }

        try {
            $module = Module::findOrFail($moduleId);
            $isActive = $this->moduleService->isModuleActiveForTenant($moduleId, $tenant->id);

            if ($isActive) {
                // Nonaktifkan modul
                $this->moduleActivationService->deactivateModule($module, $tenant);
                $message = "Modul {$module->name} berhasil dinonaktifkan";
            } else {
                // Aktifkan modul
                $this->moduleActivationService->activateModule($module, $tenant);
                $message = "Modul {$module->name} berhasil diaktifkan";
            }

            return redirect()->route('superadmin.modules')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Gagal mengubah status modul: ' . $e->getMessage());
            return back()->withErrors(['module' => 'Gagal mengubah status modul: ' . $e->getMessage()]);
        }
    }

    /**
     * Menampilkan formulir untuk menambah tenant baru 
     */
    public function createTenant()
    {
        return view('superadmin.tenants.create');
    }

    /**
     * Menyimpan tenant baru ke database
     */
    public function storeTenant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tenants,code',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:100',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Menambahkan created_by ke data yang akan disimpan
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;

        // Upload dan simpan file logo jika ada
        if ($request->hasFile('logo_path')) {
            $logoFile = $request->file('logo_path');
            $filename = time() . '_' . $logoFile->getClientOriginalName();

            // Simpan ke folder storage/app/public/tenant-logos
            $path = $logoFile->storeAs('tenant-logos', $filename, 'public');

            // Set nilai logo_path untuk disimpan ke database
            $validated['logo_path'] = $path;
        }

        $tenant = Tenant::create($validated);

        // Tambahkan akses untuk superadmin yang membuat
        Auth::user()->accessibleTenants()->attach($tenant->id);

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Rumah sakit berhasil ditambahkan');
    }

    /**
     * Proses permintaan aktivasi modul
     */
    public function processRequest(Request $request, $requestId)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $moduleRequest = ModuleActivationRequest::findOrFail($requestId);
            $status = $request->action === 'approve' ? 'approved' : 'rejected';
            $user = Auth::user();

            $this->moduleActivationService->processActivationRequest(
                $moduleRequest,
                $user,
                $status,
                $request->notes
            );

            $action = $request->action === 'approve' ? 'disetujui' : 'ditolak';
            $message = "Permintaan aktivasi modul {$moduleRequest->module->name} berhasil {$action}";

            return redirect()->route('superadmin.module-requests')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Gagal memproses permintaan aktivasi modul: ' . $e->getMessage());
            return back()->withErrors(['request' => 'Gagal memproses permintaan: ' . $e->getMessage()]);
        }
    }

    /**
     * Akses sebagai Admin RS untuk tenant yang dipilih
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function impersonateAdmin(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id'
        ]);

        try {
            $tenant = Tenant::findOrFail($request->tenant_id);

            // Simpan tenant aktif di session
            $this->tenantSelectionService->setActiveTenant($tenant->id);

            // Simpan flag bahwa user sedang mengakses sebagai admin RS
            session(['impersonating_admin' => true, 'impersonated_tenant_id' => $tenant->id]);

            return redirect()->route('dashboard')
                ->with('success', "Berhasil mengakses sebagai Admin RS untuk {$tenant->name}");
        } catch (\Exception $e) {
            Log::error('Gagal mengakses sebagai Admin RS: ' . $e->getMessage());
            return back()->withErrors(['tenant' => 'Gagal mengakses sebagai Admin RS: ' . $e->getMessage()]);
        }
    }

    /**
     * Keluar dari mode impersonasi admin RS
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exitImpersonation()
    {
        // Hapus flag impersonasi dari session
        session()->forget(['impersonating_admin', 'impersonated_tenant_id']);

        return redirect()->route('dashboard')
            ->with('success', 'Berhasil kembali ke akses Superadmin');
    }
}
