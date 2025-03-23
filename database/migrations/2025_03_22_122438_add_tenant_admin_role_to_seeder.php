<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah role TenantAdmin sudah ada
        if (!Role::where('name', 'TenantAdmin')->exists()) {
            // Buat role TenantAdmin jika belum ada
            $role = Role::create(['name' => 'TenantAdmin']);

            // Berikan izin yang sesuai untuk role TenantAdmin
            $permissions = [
                'view_dashboard',
                'create_document',
                'view_document',
                'edit_document',
                'delete_document',
                'approve_document',
                'manage_users',
                'view_users',
                'view_reports',
                'generate_reports',
                'view_audit_logs',
            ];

            $role->givePermissionTo($permissions);

            // Tambahkan izin role management
            $hierarchies = [
                // TenantAdmin dapat mengelola semua role kecuali Superadmin dan TenantAdmin lain
                ['manager' => 'TenantAdmin', 'manageable' => 'ManajemenStrategis'],
                ['manager' => 'TenantAdmin', 'manageable' => 'ManajemenEksekutif'],
                ['manager' => 'TenantAdmin', 'manageable' => 'ManajemenOperasional'],
                ['manager' => 'TenantAdmin', 'manageable' => 'Staf'],
            ];

            // Tambahkan izin untuk superadmin mengelola TenantAdmin
            $superadminRole = Role::where('name', 'Superadmin')->first();
            if ($superadminRole) {
                $hierarchies[] = ['manager' => 'Superadmin', 'manageable' => 'TenantAdmin'];
            }

            $now = now();
            $data = [];

            foreach ($hierarchies as $hierarchy) {
                $managerRole = Role::where('name', $hierarchy['manager'])->first();
                $manageableRole = Role::where('name', $hierarchy['manageable'])->first();

                if ($managerRole && $manageableRole) {
                    // Cek apakah relasi sudah ada
                    $exists = DB::table('role_management_permissions')
                        ->where('manager_role_id', $managerRole->id)
                        ->where('manageable_role_id', $manageableRole->id)
                        ->exists();

                    if (!$exists) {
                        $data[] = [
                            'manager_role_id' => $managerRole->id,
                            'manageable_role_id' => $manageableRole->id,
                            'created_at' => $now,
                            'updated_at' => $now
                        ];
                    }
                }
            }

            if (!empty($data)) {
                DB::table('role_management_permissions')->insert($data);
            }

            // Tambahkan izin modul untuk TenantAdmin
            $modules = DB::table('modules')->get();

            foreach ($modules as $module) {
                // Cek apakah izin sudah ada
                $exists = DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('module_id', $module->id)
                    ->exists();

                if (!$exists) {
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module_id' => $module->id,
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_approve' => true,
                        'can_activate' => false, // Hanya superadmin yang bisa mengaktifkan modul
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cari role TenantAdmin
        $role = Role::where('name', 'TenantAdmin')->first();

        if ($role) {
            // Hapus semua izin modul untuk TenantAdmin
            DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->delete();

            // Hapus semua izin role management untuk TenantAdmin
            DB::table('role_management_permissions')
                ->where('manager_role_id', $role->id)
                ->orWhere('manageable_role_id', $role->id)
                ->delete();

            // Hapus role TenantAdmin
            $role->delete();
        }
    }
};
