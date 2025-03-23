<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Tenant;
use App\Models\User;

class SuperadminTenantAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari role superadmin
        $superadminRole = Role::where('name', 'Superadmin')->first();

        if (!$superadminRole) {
            $this->command->info('Role Superadmin tidak ditemukan, melewati seeder superadmin_tenant_access');
            return;
        }

        // Dapatkan semua user dengan role superadmin
        $superadminUsers = User::role('Superadmin')->get();

        // Dapatkan semua tenant yang aktif
        $tenants = Tenant::where('is_active', true)->get();

        if ($superadminUsers->count() == 0 || $tenants->count() == 0) {
            $this->command->info('Tidak ada superadmin atau tenant aktif yang ditemukan');
            return;
        }

        $data = [];
        $now = now();

        foreach ($superadminUsers as $user) {
            $isFirstTenant = true;

            foreach ($tenants as $tenant) {
                $data[] = [
                    'user_id' => $user->id,
                    'tenant_id' => $tenant->id,
                    'is_default' => $isFirstTenant, // Tenant pertama menjadi default
                    'created_at' => $now,
                    'updated_at' => $now
                ];

                $isFirstTenant = false;
            }
        }

        if (!empty($data)) {
            // Hapus data yang mungkin sudah ada sebelumnya
            DB::table('superadmin_tenant_access')->truncate();

            // Insert data baru
            DB::table('superadmin_tenant_access')->insert($data);

            $this->command->info('Berhasil menambahkan akses tenant untuk superadmin');
        }
    }
}
