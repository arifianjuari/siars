<?php

namespace App\Services;

use App\Models\Module;
use App\Models\ModuleActivationRequest;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use App\Events\ModuleActivationProcessed;
use App\Events\ModuleActivationRequested;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;

class ModuleActivationService
{
    /**
     * Service untuk modul
     *
     * @var ModuleService
     */
    protected $moduleService;

    /**
     * AuditService instance
     * 
     * @var AuditService
     */
    protected $auditService;

    /**
     * Konstruktor
     *
     * @param ModuleService $moduleService
     * @param AuditService $auditService
     */
    public function __construct(ModuleService $moduleService, AuditService $auditService)
    {
        $this->moduleService = $moduleService;
        $this->auditService = $auditService;
    }

    /**
     * Mengaktifkan modul untuk tenant tertentu
     *
     * @param int $moduleId
     * @param mixed $tenantId
     * @param int $userId
     * @param array $settings
     * @return TenantModule
     */
    public function activateModule(int $moduleId, $tenantId, int $userId, array $settings = []): TenantModule
    {
        // Validasi apakah user bisa mengaktifkan modul
        $user = User::findOrFail($userId);
        $module = Module::findOrFail($moduleId);

        if (!$user->canActivateModule($module)) {
            throw new \Exception('User tidak memiliki izin untuk mengaktifkan modul ini');
        }

        // Gunakan DB transaction untuk memastikan integritas data
        DB::beginTransaction();

        try {
            // Cari tenant module yang ada
            $tenantModule = TenantModule::where('tenant_id', $tenantId)
                ->where('module_id', $moduleId)
                ->first();

            // Jika tidak ada, buat baru
            if (!$tenantModule) {
                // Insert manual menggunakan query builder
                DB::table('tenant_modules')->insert([
                    'tenant_id' => $tenantId,
                    'module_id' => $moduleId,
                    'is_active' => true,
                    'approved_by' => $userId,
                    'approved_at' => now(),
                    'created_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'settings' => !empty($settings) ? json_encode($settings) : null
                ]);

                // Ambil data yang baru saja dimasukkan
                $tenantModule = TenantModule::where('tenant_id', $tenantId)
                    ->where('module_id', $moduleId)
                    ->first();
            } else {
                // Update data yang sudah ada
                $tenantModule->is_active = true;
                $tenantModule->approved_by = $userId;
                $tenantModule->approved_at = now();
                $tenantModule->updated_by = $userId;
                $tenantModule->updated_at = now();

                if (!empty($settings)) {
                    $tenantModule->settings = $settings;
                }

                // Update manual menggunakan query builder
                DB::table('tenant_modules')
                    ->where('tenant_id', $tenantId)
                    ->where('module_id', $moduleId)
                    ->update([
                        'is_active' => true,
                        'approved_by' => $userId,
                        'approved_at' => now(),
                        'updated_by' => $userId,
                        'updated_at' => now(),
                        'settings' => !empty($settings) ? json_encode($settings) : $tenantModule->settings
                    ]);
            }

            DB::commit();

            // Log aktivitas
            Log::info("Modul {$module->name} diaktifkan untuk tenant ID {$tenantId} oleh user ID {$userId}");

            return $tenantModule;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal mengaktifkan modul: {$e->getMessage()}", [
                'module_id' => $moduleId,
                'tenant_id' => $tenantId,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Menonaktifkan modul untuk tenant tertentu
     *
     * @param int $moduleId
     * @param mixed $tenantId
     * @return bool
     */
    public function deactivateModule(int $moduleId, $tenantId): bool
    {
        $deactivated = TenantModule::where('module_id', $moduleId)
            ->where('tenant_id', $tenantId)
            ->update(['is_active' => false]);

        if ($deactivated) {
            $module = Module::find($moduleId);
            Log::info("Modul {$module->name} dinonaktifkan untuk tenant ID {$tenantId}");
        }

        return (bool) $deactivated;
    }

    /**
     * Membuat permintaan aktivasi modul
     *
     * @param int $moduleId
     * @param mixed $tenantId
     * @param int $requestedBy
     * @param string $notes
     * @return ModuleActivationRequest
     */
    public function requestActivation(int $moduleId, $tenantId, int $requestedBy, string $notes = ''): ModuleActivationRequest
    {
        // Validasi apakah modul sudah aktif
        if ($this->moduleService->isModuleActiveForTenant($moduleId, $tenantId)) {
            throw new \Exception('Modul sudah aktif untuk tenant ini');
        }

        // Validasi apakah sudah ada permintaan yang pending
        $pendingRequest = ModuleActivationRequest::where('module_id', $moduleId)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->exists();

        if ($pendingRequest) {
            throw new \Exception('Sudah ada permintaan aktivasi yang belum diproses untuk modul ini');
        }

        // Buat permintaan baru
        $request = new ModuleActivationRequest([
            'module_id' => $moduleId,
            'tenant_id' => $tenantId,
            'requested_by' => $requestedBy,
            'notes' => $notes,
            'requested_at' => now(),
            'status' => 'pending'
        ]);

        $request->save();

        return $request;
    }

    /**
     * Memproses permintaan aktivasi modul
     *
     * @param ModuleActivationRequest $request
     * @param User $processor
     * @param string $status
     * @param string|null $notes
     * @return ModuleActivationRequest
     */
    public function processActivationRequest(
        ModuleActivationRequest $request,
        User $processor,
        string $status,
        ?string $notes = null
    ): ModuleActivationRequest {
        DB::beginTransaction();

        try {
            Log::info("Memulai proses permintaan aktivasi dengan ID: {$request->id}, status: {$status}");

            // Log nilai sebelum perubahan
            Log::info("Status sebelum perubahan: {$request->status}, processed_by: {$request->processed_by}, processed_at: " . ($request->processed_at ? $request->processed_at->toDateTimeString() : 'null'));

            // Update status permintaan
            $request->status = $status;
            $request->processed_by = $processor->id;
            $request->processed_at = Carbon::now();
            if ($notes !== null) {
                $request->notes = $notes;
            }

            $saved = $request->save();

            Log::info("Hasil penyimpanan permintaan: " . ($saved ? 'Berhasil' : 'Gagal'));
            Log::info("Status setelah perubahan: {$request->status}, processed_by: {$request->processed_by}, processed_at: " . ($request->processed_at ? $request->processed_at->toDateTimeString() : 'null'));

            // Jika disetujui, aktivasi modul untuk tenant
            if ($status === 'approved') {
                Log::info("Permintaan disetujui, aktivasi modul {$request->module_id} untuk tenant {$request->tenant_id}");
                $this->activateModule($request->module_id, $request->tenant_id, $processor->id);
            }

            // Catat aktivitas
            $this->auditService->logRequestProcessed($request, $notes);

            // Commit transaksi
            DB::commit();
            Log::info("Transaksi di-commit");

            // Trigger event
            event(new ModuleActivationProcessed($request));

            Log::info("Permintaan aktivasi modul {$request->id} telah diproses dengan status: {$status}");

            // Refresh model dari database untuk memastikan data terbaru
            $request->refresh();
            Log::info("Status akhir setelah refresh: {$request->status}");

            return $request;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal memproses permintaan aktivasi modul: {$e->getMessage()}", [
                'request_id' => $request->id,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Helper untuk mendapatkan instance user
     *
     * @param User|int|null $user
     * @return User
     */
    protected function resolveUser($user): User
    {
        if ($user === null) {
            return Auth::user();
        }

        if ($user instanceof User) {
            return $user;
        }

        return User::findOrFail($user);
    }

    /**
     * Mengaktifkan modul untuk tenant
     *
     * @param Module $module
     * @param Tenant $tenant
     * @param User|null $user
     * @param array $options
     * @return bool
     */
    public function activateModuleForTenant(
        Module $module,
        Tenant $tenant,
        ?User $user = null,
        array $options = []
    ): bool {
        DB::beginTransaction();

        try {
            // Cek apakah modul sudah aktif
            if ($this->moduleService->isModuleActiveForTenant($module->id, $tenant->id)) {
                Log::info("Modul {$module->name} sudah aktif untuk tenant {$tenant->name}");
                DB::commit();
                return true;
            }

            // Resolve user
            $user = $this->resolveUser($user);

            // Cari atau buat tenant module
            $tenantModule = TenantModule::where('tenant_id', $tenant->id)
                ->where('module_id', $module->id)
                ->first();

            if (!$tenantModule) {
                // Buat baru jika tidak ada
                $tenantModule = new TenantModule();
                $tenantModule->tenant_id = $tenant->id;
                $tenantModule->module_id = $module->id;
                $tenantModule->created_by = $user->id;
            }

            // Update data modul tenant
            $tenantModule->is_active = true;
            $tenantModule->approved_by = $user->id;
            $tenantModule->approved_at = now();

            // Tambahkan settings jika ada
            if (!empty($options['settings'])) {
                $tenantModule->settings = $options['settings'];
            }

            // Save perubahan
            $tenantModule->save();

            // Catat aktivitas
            $this->auditService->logModuleActivated($module, $tenant, $user, $options['notes'] ?? null);

            // Commit transaksi
            DB::commit();

            Log::info("Modul {$module->name} berhasil diaktifkan untuk tenant {$tenant->name}");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal mengaktifkan modul: {$e->getMessage()}", [
                'module_id' => $module->id,
                'tenant_id' => $tenant->id,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Menonaktifkan modul untuk tenant
     *
     * @param Module $module
     * @param Tenant $tenant
     * @param User|null $user
     * @param string|null $notes
     * @return bool
     */
    public function deactivateModuleForTenant(
        Module $module,
        Tenant $tenant,
        ?User $user = null,
        ?string $notes = null
    ): bool {
        DB::beginTransaction();

        try {
            // Cek apakah modul aktif
            if (!$this->moduleService->isModuleActiveForTenant($module->id, $tenant->id)) {
                Log::info("Modul {$module->name} sudah nonaktif untuk tenant {$tenant->name}");
                DB::commit();
                return true;
            }

            // Resolve user
            $user = $this->resolveUser($user);

            // Dapatkan tenant module
            $tenantModule = TenantModule::where('tenant_id', $tenant->id)
                ->where('module_id', $module->id)
                ->first();

            if ($tenantModule) {
                // Nonaktifkan modul
                $tenantModule->is_active = false;
                $tenantModule->updated_by = $user->id;
                $tenantModule->save();
            }

            // Catat aktivitas
            $this->auditService->logModuleDeactivated($module, $tenant, $user, $notes);

            // Commit transaksi
            DB::commit();

            Log::info("Modul {$module->name} berhasil dinonaktifkan untuk tenant {$tenant->name}");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menonaktifkan modul: {$e->getMessage()}", [
                'module_id' => $module->id,
                'tenant_id' => $tenant->id,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Membuat permintaan aktivasi modul
     *
     * @param Module $module
     * @param Tenant $tenant
     * @param User $requester
     * @param string|null $notes
     * @return ModuleActivationRequest
     */
    public function requestModuleActivation(
        Module $module,
        Tenant $tenant,
        User $requester,
        ?string $notes = null
    ): ModuleActivationRequest {
        DB::beginTransaction();

        try {
            // Validasi apakah modul sudah aktif
            if ($this->moduleService->isModuleActiveForTenant($module->id, $tenant->id)) {
                throw new \Exception("Modul {$module->name} sudah aktif untuk tenant {$tenant->name}");
            }

            // Cek apakah sudah ada permintaan yang pending
            $pendingRequest = ModuleActivationRequest::where('module_id', $module->id)
                ->where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->first();

            if ($pendingRequest) {
                throw new \Exception("Sudah ada permintaan aktivasi modul {$module->name} yang tertunda untuk tenant {$tenant->name}");
            }

            // Buat permintaan baru
            $request = new ModuleActivationRequest([
                'module_id' => $module->id,
                'tenant_id' => $tenant->id,
                'requested_by' => $requester->id,
                'status' => 'pending',
                'requested_at' => Carbon::now(),
                'notes' => $notes,
            ]);

            $request->save();

            // Catat aktivitas
            $this->auditService->logActivationRequest($request);

            // Commit transaksi
            DB::commit();

            // Trigger event
            event(new ModuleActivationRequested($request));

            Log::info("Permintaan aktivasi modul {$module->name} untuk tenant {$tenant->name} berhasil dibuat");

            return $request;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal membuat permintaan aktivasi modul: {$e->getMessage()}", [
                'module_id' => $module->id,
                'tenant_id' => $tenant->id,
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
