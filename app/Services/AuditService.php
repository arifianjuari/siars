<?php

namespace App\Services;

use App\Models\Module;
use App\Models\ModuleActivationLog;
use App\Models\ModuleActivationRequest;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantModule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AuditService
{
    /**
     * Mencatat aktivitas aktivasi modul
     *
     * @param string $action
     * @param Module $module
     * @param Tenant $tenant
     * @param User|null $user
     * @param ModuleActivationRequest|null $request
     * @param string|null $notes
     * @param array|null $oldData
     * @param array|null $newData
     * @return ModuleActivationLog
     */
    public function logModuleActivation(
        string $action,
        Module $module,
        Tenant $tenant,
        ?User $user = null,
        ?ModuleActivationRequest $request = null,
        ?string $notes = null,
        ?array $oldData = null,
        ?array $newData = null
    ): ModuleActivationLog {
        try {
            // Jika user tidak diberikan, gunakan user saat ini
            if (!$user && Auth::check()) {
                $user = Auth::user();
            }

            $log = new ModuleActivationLog([
                'module_id' => $module->id,
                'tenant_id' => $tenant->id,
                'request_id' => $request ? $request->id : null,
                'user_id' => $user ? $user->id : null,
                'action' => $action,
                'notes' => $notes,
                'old_data' => $oldData,
                'new_data' => $newData,
                'action_at' => now(),
            ]);

            $log->save();

            Log::info("Aktivitas modul dicatat: {$action} untuk modul {$module->name} di tenant {$tenant->name}");

            return $log;
        } catch (\Exception $e) {
            Log::error("Gagal mencatat aktivitas modul: {$e->getMessage()}", [
                'exception' => $e,
                'module_id' => $module->id,
                'tenant_id' => $tenant->id,
                'action' => $action,
            ]);

            throw $e;
        }
    }

    /**
     * Mencatat permintaan aktivasi modul
     *
     * @param ModuleActivationRequest $request
     * @return ModuleActivationLog
     */
    public function logActivationRequest(ModuleActivationRequest $request): ModuleActivationLog
    {
        return $this->logModuleActivation(
            'requested',
            $request->module,
            $request->tenant,
            $request->requester,
            $request,
            $request->notes,
            null,
            [
                'request_id' => $request->id,
                'status' => $request->status,
                'requested_at' => $request->requested_at->format('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * Mencatat pemrosesan permintaan aktivasi modul
     *
     * @param ModuleActivationRequest $request
     * @param string|null $notes
     * @return ModuleActivationLog
     */
    public function logRequestProcessed(ModuleActivationRequest $request, ?string $notes = null): ModuleActivationLog
    {
        $oldData = [
            'status' => 'pending',
        ];

        $newData = [
            'status' => $request->status,
            'processed_at' => $request->processed_at->format('Y-m-d H:i:s'),
            'processed_by' => $request->processed_by,
        ];

        $action = $request->status === 'approved' ? 'approved' : 'rejected';

        return $this->logModuleActivation(
            $action,
            $request->module,
            $request->tenant,
            $request->processor,
            $request,
            $notes ?? $request->notes,
            $oldData,
            $newData
        );
    }

    /**
     * Mencatat aktivasi modul
     *
     * @param Module $module
     * @param Tenant $tenant
     * @param User|null $user
     * @param string|null $notes
     * @return ModuleActivationLog
     */
    public function logModuleActivated(Module $module, Tenant $tenant, ?User $user = null, ?string $notes = null): ModuleActivationLog
    {
        return $this->logModuleActivation(
            'activated',
            $module,
            $tenant,
            $user,
            null,
            $notes,
            ['active' => false],
            ['active' => true]
        );
    }

    /**
     * Mencatat deaktivasi modul
     *
     * @param Module $module
     * @param Tenant $tenant
     * @param User|null $user
     * @param string|null $notes
     * @return ModuleActivationLog
     */
    public function logModuleDeactivated(Module $module, Tenant $tenant, ?User $user = null, ?string $notes = null): ModuleActivationLog
    {
        return $this->logModuleActivation(
            'deactivated',
            $module,
            $tenant,
            $user,
            null,
            $notes,
            ['active' => true],
            ['active' => false]
        );
    }

    /**
     * Mendapatkan histori aktivasi modul untuk tenant
     * 
     * @param Tenant|null $tenant
     * @param string|null $startDate Format Y-m-d
     * @param string|null $endDate Format Y-m-d
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getModuleActivationHistory(?Tenant $tenant = null, ?string $startDate = null, ?string $endDate = null, ?int $limit = null)
    {
        $query = ModuleActivationLog::with(['module', 'user', 'request', 'tenant'])
            ->orderBy('action_at', 'desc');

        if ($tenant) {
            $query->forTenant($tenant->id);
        }

        if ($startDate && $endDate) {
            $query->betweenDates(
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            );
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Mendapatkan statistik aktivasi modul
     * 
     * @param string|null $startDate Format Y-m-d
     * @param string|null $endDate Format Y-m-d
     * @return array
     */
    public function getModuleActivationStats(?string $startDate = null, ?string $endDate = null): array
    {
        $query = ModuleActivationLog::query();

        if ($startDate && $endDate) {
            $query->whereBetween('action_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        $totalActivated = $query->clone()->ofAction('activated')->count();
        $totalDeactivated = $query->clone()->ofAction('deactivated')->count();
        $totalRequested = $query->clone()->ofAction('requested')->count();
        $totalApproved = $query->clone()->ofAction('approved')->count();
        $totalRejected = $query->clone()->ofAction('rejected')->count();

        return [
            'total_activated' => $totalActivated,
            'total_deactivated' => $totalDeactivated,
            'total_requested' => $totalRequested,
            'total_approved' => $totalApproved,
            'total_rejected' => $totalRejected,
            'acceptance_rate' => $totalRequested > 0
                ? round(($totalApproved / $totalRequested) * 100, 2)
                : 0,
        ];
    }

    /**
     * Mendapatkan daftar modul aktif per tenant
     * 
     * @param Tenant|null $tenant
     * @return Collection
     */
    public function getActiveModulesByTenant(?Tenant $tenant = null): Collection
    {
        $query = DB::table('tenant_modules')
            ->join('modules', 'tenant_modules.module_id', '=', 'modules.id')
            ->join('tenants', 'tenant_modules.tenant_id', '=', 'tenants.id')
            ->leftJoin('users as approver', 'tenant_modules.approved_by', '=', 'approver.id')
            ->select([
                'tenants.id as tenant_id',
                'tenants.name as tenant_name',
                'tenants.code as tenant_code',
                'modules.id as module_id',
                'modules.name as module_name',
                'modules.code as module_code',
                'modules.description as module_description',
                'tenant_modules.approved_at as activated_at',
                'tenant_modules.is_active as status',
                'approver.name as approved_by_name',
            ])
            ->where('tenant_modules.is_active', true);

        if ($tenant) {
            $query->where('tenants.id', $tenant->id);
        }

        return $query->get();
    }

    /**
     * Mendapatkan riwayat aktivasi modul dalam format yang sudah diproses
     * 
     * @param Collection $logs
     * @return Collection
     */
    public function transformModuleHistoryLogs(Collection $logs): Collection
    {
        return $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'action' => $log->action,
                'action_text' => $log->action_text,
                'action_at' => $log->action_at->format('Y-m-d H:i:s'),
                'module_id' => $log->module_id,
                'module_name' => $log->module->name,
                'module_code' => $log->module->code,
                'tenant_id' => $log->tenant_id,
                'tenant_name' => $log->tenant->name,
                'tenant_code' => $log->tenant->code,
                'user_id' => $log->user_id,
                'user_name' => $log->user ? $log->user->name : 'System',
                'notes' => $log->notes,
                'request_id' => $log->request_id,
            ];
        });
    }
}
