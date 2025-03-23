<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleActivationRequest;
use App\Models\Tenant;
use App\Services\ModuleActivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModuleActivationRequestController extends Controller
{
    /**
     * Service untuk aktivasi modul
     *
     * @var ModuleActivationService
     */
    protected $moduleActivationService;

    /**
     * Konstruktor
     *
     * @param ModuleActivationService $moduleActivationService
     */
    public function __construct(ModuleActivationService $moduleActivationService)
    {
        $this->moduleActivationService = $moduleActivationService;
    }

    /**
     * Menampilkan daftar permintaan aktivasi modul
     * 
     * Untuk superadmin: semua permintaan
     * Untuk admin RS: hanya permintaan dari tenant mereka
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = null;
        $status = $request->input('status', 'pending');

        // Jika user adalah admin RS, hanya tampilkan permintaan dari tenant mereka
        if (!$user->isSuperadmin() && $user->tenant_id) {
            $tenantId = $user->tenant_id;
        } else if ($request->has('tenant_id')) {
            // Jika superadmin, bisa filter berdasarkan tenant
            $tenantId = $request->input('tenant_id');
        }

        $query = ModuleActivationRequest::with(['tenant', 'module', 'requestedBy', 'processedBy'])
            ->orderBy('requested_at', 'desc');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(10);

        // Jika superadmin, sediakan daftar tenant untuk filter
        $tenants = [];
        if ($user->isSuperadmin()) {
            $tenants = Tenant::orderBy('name')->get();
        }

        return view('module-activation.index', [
            'requests' => $requests,
            'tenants' => $tenants,
            'selectedTenant' => $tenantId,
            'selectedStatus' => $status
        ]);
    }

    /**
     * Menampilkan form untuk membuat permintaan aktivasi modul
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $user = Auth::user();

        // Jika user tidak terhubung dengan tenant, berikan pesan error
        if (!$user->tenant_id) {
            return redirect()->back()
                ->withErrors(['tenant' => 'Anda tidak terhubung dengan rumah sakit manapun']);
        }

        // Dapatkan daftar modul yang belum aktif untuk tenant
        $tenant = Tenant::with('modules')->find($user->tenant_id);

        // ID modul yang sudah aktif
        $activeModuleIds = $tenant->modules()
            ->wherePivot('is_active', true)
            ->pluck('modules.id')
            ->toArray();

        // Modul yang belum aktif
        $inactiveModules = Module::whereNotIn('id', $activeModuleIds)->get();

        return view('module-activation.create', [
            'tenant' => $tenant,
            'modules' => $inactiveModules
        ]);
    }

    /**
     * Menyimpan permintaan aktivasi modul
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'notes' => 'nullable|string'
        ]);

        $user = Auth::user();

        // Jika user tidak terhubung dengan tenant, berikan pesan error
        if (!$user->tenant_id) {
            return redirect()->back()
                ->withErrors(['tenant' => 'Anda tidak terhubung dengan rumah sakit manapun']);
        }

        try {
            $request = $this->moduleActivationService->requestActivation(
                $validated['module_id'],
                $user->tenant_id,
                $user->id,
                $validated['notes'] ?? ''
            );

            return redirect()->route('module-activation.index')
                ->with('success', 'Permintaan aktivasi modul berhasil dibuat dan menunggu persetujuan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal membuat permintaan aktivasi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Menampilkan detail permintaan aktivasi
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        $request = ModuleActivationRequest::with(['tenant', 'module', 'requestedBy', 'processedBy'])
            ->findOrFail($id);

        // Jika bukan superadmin, verifikasi permintaan milik tenant mereka
        if (!$user->isSuperadmin() && $user->tenant_id != $request->tenant_id) {
            abort(403, 'Anda tidak memiliki akses ke permintaan ini');
        }

        return view('module-activation.show', [
            'request' => $request
        ]);
    }

    /**
     * Memproses permintaan aktivasi (approve/reject)
     * 
     * Hanya untuk superadmin
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string'
        ]);

        $user = Auth::user();

        // Validasi apakah user adalah superadmin
        if (!$user->isSuperadmin()) {
            abort(403, 'Hanya superadmin yang dapat memproses permintaan aktivasi');
        }

        $activationRequest = ModuleActivationRequest::findOrFail($id);

        // Validasi status permintaan
        if ($activationRequest->status !== 'pending') {
            return redirect()->back()
                ->withErrors(['status' => 'Permintaan ini sudah diproses sebelumnya']);
        }

        try {
            $status = $validated['action'] === 'approve' ? 'approved' : 'rejected';
            $notes = $validated['notes'] ?? '';

            // Gunakan DB transaction
            DB::beginTransaction();

            // Update status permintaan menggunakan query builder langsung
            DB::table('module_activation_requests')
                ->where('id', $id)
                ->update([
                    'status' => $status,
                    'processed_by' => $user->id,
                    'processed_at' => now(),
                    'notes' => $notes ? $notes : DB::raw('notes'),
                    'updated_at' => now()
                ]);

            // Jika disetujui, aktivasi modul untuk tenant
            if ($status === 'approved') {
                $this->moduleActivationService->activateModule(
                    $activationRequest->module_id,
                    $activationRequest->tenant_id,
                    $user->id
                );
            }

            // Commit transaksi
            DB::commit();

            // Refresh objek dari database
            $activationRequest = ModuleActivationRequest::findOrFail($id);

            // Trigger event (optional)
            event(new \App\Events\ModuleActivationProcessed($activationRequest));

            $message = $validated['action'] === 'approve'
                ? 'Permintaan aktivasi modul berhasil disetujui'
                : 'Permintaan aktivasi modul berhasil ditolak';

            return redirect()->route('module-activation.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error memproses permintaan aktivasi: " . $e->getMessage(), [
                'request_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Gagal memproses permintaan: ' . $e->getMessage()]);
        }
    }
}
