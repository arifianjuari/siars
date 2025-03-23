<?php

namespace App\Services;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use Illuminate\Support\Collection;

class ModuleService
{
    /**
     * Mendapatkan semua modul yang tersedia di sistem
     *
     * @param bool $activeOnly Jika true, hanya mengembalikan modul yang aktif
     * @return Collection
     */
    public function getAllModules(bool $activeOnly = false): Collection
    {
        $query = Module::query();

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('order')->get();
    }

    /**
     * Mendapatkan modul yang aktif di sistem
     *
     * @return Collection
     */
    public function getActiveModules(): Collection
    {
        return $this->getAllModules(true);
    }

    /**
     * Mendapatkan modul berdasarkan tenant
     *
     * @param Tenant|string $tenant Instance tenant atau ID tenant
     * @param bool $activeOnly Jika true, hanya mengembalikan modul yang aktif untuk tenant
     * @return Collection
     */
    public function getModulesByTenant($tenant, bool $activeOnly = false): Collection
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        $query = TenantModule::where('tenant_id', $tenantId)
            ->with('module');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->get()->map(function ($tenantModule) {
            $module = $tenantModule->module;
            $module->is_activated = $tenantModule->is_active;
            $module->settings = $tenantModule->settings;
            $module->approved_by = $tenantModule->approved_by;
            $module->approved_at = $tenantModule->approved_at;

            return $module;
        });
    }

    /**
     * Mendapatkan semua modul yang aktif untuk tenant tertentu
     * 
     * @param mixed $tenantId
     * @return Collection
     */
    public function getActiveTenantModules($tenantId): Collection
    {
        return TenantModule::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with('module')
            ->get()
            ->pluck('module');
    }

    /**
     * Mengecek apakah modul aktif untuk tenant tertentu
     * 
     * @param int $moduleId
     * @param mixed $tenantId
     * @return bool
     */
    public function isModuleActiveForTenant(int $moduleId, $tenantId): bool
    {
        return TenantModule::where('module_id', $moduleId)
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->whereNotNull('approved_by')
            ->whereNotNull('approved_at')
            ->exists();
    }

    /**
     * Mengaktifkan modul untuk tenant tertentu
     * 
     * @param int $moduleId
     * @param mixed $tenantId
     * @param int $approvedBy
     * @return TenantModule
     */
    public function activateModule(int $moduleId, $tenantId, int $approvedBy): TenantModule
    {
        $tenantModule = TenantModule::firstOrNew([
            'module_id' => $moduleId,
            'tenant_id' => $tenantId,
        ]);

        $tenantModule->is_active = true;
        $tenantModule->approved_by = $approvedBy;
        $tenantModule->approved_at = now();
        $tenantModule->save();

        return $tenantModule;
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
        return TenantModule::where('module_id', $moduleId)
            ->where('tenant_id', $tenantId)
            ->update(['is_active' => false]);
    }
}
