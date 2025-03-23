<?php

namespace App\Traits;

use App\Models\Module;
use App\Models\RoleModulePermission;
use Illuminate\Support\Facades\Cache;

trait HasModulePermissions
{
    /**
     * Memeriksa apakah user memiliki izin pada modul tertentu
     *
     * @param Module|int $module Modul atau ID modul
     * @param string $permission Jenis izin (view, create, edit, delete, approve, activate)
     * @return bool
     */
    public function hasModulePermission($module, string $permission): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        $moduleId = $module instanceof Module ? $module->id : $module;
        $roleId = $this->roles()->first()?->id;

        if (!$roleId) {
            return false;
        }

        $cacheKey = "user_{$this->id}_role_{$roleId}_module_{$moduleId}_permission_{$permission}";

        return Cache::remember($cacheKey, 60, function () use ($moduleId, $roleId, $permission) {
            return RoleModulePermission::where('role_id', $roleId)
                ->where('module_id', $moduleId)
                ->where("can_{$permission}", true)
                ->exists();
        });
    }

    /**
     * Memeriksa apakah user dapat melihat modul tertentu
     *
     * @param Module|int $module
     * @return bool
     */
    public function canViewModule($module): bool
    {
        return $this->hasModulePermission($module, 'view');
    }

    /**
     * Memeriksa apakah user dapat membuat data pada modul tertentu
     *
     * @param Module|int $module
     * @return bool
     */
    public function canCreateInModule($module): bool
    {
        return $this->hasModulePermission($module, 'create');
    }

    /**
     * Memeriksa apakah user dapat mengedit data pada modul tertentu
     *
     * @param Module|int $module
     * @return bool
     */
    public function canEditInModule($module): bool
    {
        return $this->hasModulePermission($module, 'edit');
    }

    /**
     * Memeriksa apakah user dapat menghapus data pada modul tertentu
     *
     * @param Module|int $module
     * @return bool
     */
    public function canDeleteInModule($module): bool
    {
        return $this->hasModulePermission($module, 'delete');
    }

    /**
     * Memeriksa apakah user dapat menyetujui data pada modul tertentu
     *
     * @param Module|int $module
     * @return bool
     */
    public function canApproveInModule($module): bool
    {
        return $this->hasModulePermission($module, 'approve');
    }

    /**
     * Memeriksa apakah user dapat mengaktifkan modul tertentu
     *
     * @param Module|int $module
     * @return bool
     */
    public function canActivateModule($module): bool
    {
        return $this->hasModulePermission($module, 'activate');
    }

    /**
     * Memeriksa apakah user adalah superadmin
     *
     * @return bool
     */
    public function isSuperadmin(): bool
    {
        return $this->hasRole('Superadmin');
    }
}
