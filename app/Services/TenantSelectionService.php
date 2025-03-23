<?php

namespace App\Services;

use App\Models\SuperadminTenantAccess;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TenantSelectionService
{
    // Kunci untuk menyimpan ID tenant aktif di session
    protected const SESSION_KEY = 'active_tenant_id';

    /**
     * Mengatur tenant aktif untuk user saat ini
     *
     * @param Tenant|string $tenant Instance tenant atau ID tenant
     * @param User|null $user User yang aktif (default: user saat ini)
     * @return Tenant Instance tenant yang diaktifkan
     * @throws AuthorizationException
     */
    public function setActiveTenant($tenant, ?User $user = null): Tenant
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            throw new \RuntimeException('Tidak ada user yang sedang aktif.');
        }

        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        // Validasi akses user ke tenant
        if (!$this->userCanAccessTenant($user, $tenantId)) {
            throw new AuthorizationException('Anda tidak memiliki akses ke tenant ini.');
        }

        // Simpan tenant aktif ke session
        Session::put(self::SESSION_KEY, $tenantId);

        // Jika user adalah superadmin, update default tenant
        if ($user->isSuperadmin()) {
            $this->updateSuperadminDefaultTenant($user, $tenantId);
        }

        return $tenant instanceof Tenant ? $tenant : Tenant::findOrFail($tenantId);
    }

    /**
     * Mendapatkan tenant aktif untuk user saat ini
     *
     * @param User|null $user User yang aktif (default: user saat ini)
     * @return Tenant|null Instance tenant yang aktif atau null jika tidak ada
     */
    public function getActiveTenant(?User $user = null): ?Tenant
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return null;
        }

        // Coba ambil dari session
        $tenantId = Session::get(self::SESSION_KEY);

        // Jika tidak ada di session, coba ambil dari relasi user
        if (!$tenantId) {
            if ($user->isSuperadmin()) {
                // Superadmin: ambil dari default tenant
                $defaultTenant = $user->defaultTenant();
                if ($defaultTenant) {
                    // Simpan ke session untuk selanjutnya
                    Session::put(self::SESSION_KEY, $defaultTenant->id);
                    return $defaultTenant;
                }

                // Jika tidak ada default, ambil tenant pertama yang bisa diakses
                $firstTenant = $user->accessibleTenants()->first();
                if ($firstTenant) {
                    // Simpan ke session dan update default
                    Session::put(self::SESSION_KEY, $firstTenant->id);
                    $this->updateSuperadminDefaultTenant($user, $firstTenant->id);
                    return $firstTenant;
                }
            } else {
                // Non-superadmin: ambil dari relasi tenant
                $tenant = $user->tenant;
                if ($tenant) {
                    // Simpan ke session untuk selanjutnya
                    Session::put(self::SESSION_KEY, $tenant->id);
                    return $tenant;
                }
            }

            return null;
        }

        // Validasi bahwa user masih memiliki akses ke tenant
        if (!$this->userCanAccessTenant($user, $tenantId)) {
            // Jika tidak bisa akses lagi, hapus dari session
            Session::forget(self::SESSION_KEY);
            return $this->getActiveTenant($user); // Rekursif untuk mendapatkan tenant alternatif
        }

        return Tenant::find($tenantId);
    }

    /**
     * Memeriksa apakah user dapat mengakses tenant tertentu
     *
     * @param User $user User yang akan dicek
     * @param Tenant|string $tenant Instance tenant atau ID tenant
     * @return bool
     */
    public function userCanAccessTenant(User $user, $tenant): bool
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        // Superadmin: cek akses dari tabel superadmin_tenant_access
        if ($user->isSuperadmin()) {
            return SuperadminTenantAccess::where('user_id', $user->id)
                ->where('tenant_id', $tenantId)
                ->exists();
        }

        // Non-superadmin: hanya bisa akses tenant sendiri
        return $user->tenant_id === $tenantId;
    }

    /**
     * Mendapatkan semua tenant yang dapat diakses oleh user
     *
     * @param User|null $user User yang aktif (default: user saat ini)
     * @param bool $activeOnly Apakah hanya menampilkan tenant yang aktif
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccessibleTenants(?User $user = null, bool $activeOnly = true)
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return collect([]);
        }

        // Superadmin: ambil dari relasi accessibleTenants
        if ($user->isSuperadmin()) {
            $query = $user->accessibleTenants();

            // Filter berdasarkan status aktif jika diminta
            if ($activeOnly) {
                return $user->accessibleTenants->where('is_active', true);
            }

            return $user->accessibleTenants;
        }

        // Non-superadmin: hanya bisa akses tenant sendiri
        if ($user->tenant) {
            if (!$activeOnly || $user->tenant->is_active) {
                return collect([$user->tenant]);
            }
        }

        return collect([]);
    }

    /**
     * Update tenant default untuk superadmin
     *
     * @param User $user Superadmin user
     * @param string $tenantId ID tenant yang akan dijadikan default
     * @return void
     */
    protected function updateSuperadminDefaultTenant(User $user, string $tenantId): void
    {
        if (!$user->isSuperadmin()) {
            return;
        }

        // Reset semua is_default ke false
        SuperadminTenantAccess::where('user_id', $user->id)
            ->update(['is_default' => false]);

        // Set yang baru menjadi default
        SuperadminTenantAccess::where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->update(['is_default' => true]);
    }

    /**
     * Menghapus tenant aktif dari sesi
     *
     * @return void
     */
    public function clearActiveTenant()
    {
        Session::forget(self::SESSION_KEY);
    }
}
