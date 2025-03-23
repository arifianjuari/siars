<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Models\SuperadminTenantAccess;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ubah ini jika perlu
        $password = Hash::make('password123');

        // Cek apakah tenant sudah ada, jika tidak buat tenant baru
        $tenant = Tenant::first();

        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'Rumah Sakit Dummy',
                'code' => 'RSD001',
                'is_active' => true,
                'created_by' => 1,
            ]);
            $this->command->info('Tenant baru dibuat: ' . $tenant->name);
        } else {
            $this->command->info('Menggunakan tenant yang sudah ada: ' . $tenant->name);
        }

        // Ambil semua tenant aktif untuk diberikan ke superadmin
        $allTenants = Tenant::where('is_active', true)->get();

        // Daftar role yang akan dibuat user dummy
        $roles = ['Superadmin', 'TenantAdmin', 'ManajemenStrategis', 'ManajemenEksekutif', 'ManajemenOperasional', 'Staf'];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $this->command->error("Role $roleName tidak ditemukan!");
                continue;
            }

            // Tentukan apakah user memiliki tenant_id atau tidak (Superadmin tidak terkait dengan tenant)
            $tenantId = ($roleName === 'Superadmin') ? null : $tenant->id;

            // Buat user baru
            $user = User::create([
                'name' => "Dummy $roleName",
                'email' => strtolower(str_replace(' ', '', $roleName)) . '@dummy.com',
                'password' => $password,
                'is_active' => true,
                'tenant_id' => $tenantId,
            ]);

            // Assign role
            $user->assignRole($roleName);

            // Jika role adalah Superadmin, tambahkan akses ke semua tenant
            if ($roleName === 'Superadmin' && $allTenants->count() > 0) {
                $isFirstTenant = true;

                foreach ($allTenants as $tenantItem) {
                    SuperadminTenantAccess::create([
                        'user_id' => $user->id,
                        'tenant_id' => $tenantItem->id,
                        'is_default' => $isFirstTenant // Tenant pertama menjadi default
                    ]);

                    $isFirstTenant = false;
                }

                $this->command->info("  └── Akses ke " . $allTenants->count() . " tenant telah diberikan ke $roleName");
            }

            $this->command->info("User $roleName berhasil dibuat dengan email: " . $user->email);
        }
    }
}
