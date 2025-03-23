<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Models\SuperadminTenantAccess;
use Spatie\Permission\Models\Role;

class FixSuperadminAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua user dengan role Superadmin
        $superadmins = User::role('Superadmin')->get();

        if ($superadmins->count() == 0) {
            $this->command->warning('Tidak ada superadmin yang ditemukan!');
            return;
        }

        // Ambil semua tenant aktif
        $tenants = Tenant::where('is_active', true)->get();

        if ($tenants->count() == 0) {
            $this->command->warning('Tidak ada tenant aktif yang ditemukan!');
            return;
        }

        foreach ($superadmins as $superadmin) {
            $this->command->info("Memproses superadmin: {$superadmin->name} ({$superadmin->email})");

            // Dapatkan tenant yang sudah diakses oleh superadmin ini
            $existingTenantIds = SuperadminTenantAccess::where('user_id', $superadmin->id)
                ->pluck('tenant_id')
                ->toArray();

            // Dapatkan tenant default superadmin
            $defaultTenant = SuperadminTenantAccess::where('user_id', $superadmin->id)
                ->where('is_default', true)
                ->first();

            $isAnyNew = false;

            foreach ($tenants as $tenant) {
                // Jika tenant belum diakses oleh superadmin, tambahkan akses
                if (!in_array($tenant->id, $existingTenantIds)) {
                    SuperadminTenantAccess::create([
                        'user_id' => $superadmin->id,
                        'tenant_id' => $tenant->id,
                        'is_default' => !$defaultTenant && !$isAnyNew // Jadikan default jika belum ada default dan ini yang pertama
                    ]);

                    $isAnyNew = true;
                    $this->command->info("  └── Menambahkan akses ke tenant: {$tenant->name}");
                }
            }

            if (!$isAnyNew) {
                $this->command->info("  └── Superadmin sudah memiliki akses ke semua tenant");
            }
        }

        $this->command->info("Selesai memperbaiki akses superadmin. Semua superadmin sekarang dapat mengakses semua tenant.");
    }
}
