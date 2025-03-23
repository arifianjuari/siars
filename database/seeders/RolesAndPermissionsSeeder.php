<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard permissions
            'view_dashboard',

            // Document permissions
            'create_document',
            'view_document',
            'edit_document',
            'delete_document',
            'approve_document',

            // User management
            'manage_users',
            'view_users',

            // Settings
            'manage_settings',

            // Reports
            'view_reports',
            'generate_reports',

            // Audit
            'view_audit_logs',
        ];

        foreach ($permissions as $permission) {
            // Cek apakah permission sudah ada sebelum membuatnya
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Create roles and assign permissions

        // 1. Superadmin - has all permissions
        if (!Role::where('name', 'Superadmin')->exists()) {
            $role = Role::create(['name' => 'Superadmin']);
            $role->givePermissionTo(Permission::all());
        } else {
            $role = Role::where('name', 'Superadmin')->first();
            $role->syncPermissions(Permission::all());
        }

        // 2. ManajemenStrategis - dashboard access and document approval
        if (!Role::where('name', 'ManajemenStrategis')->exists()) {
            $role = Role::create(['name' => 'ManajemenStrategis']);
        } else {
            $role = Role::where('name', 'ManajemenStrategis')->first();
        }
        $role->syncPermissions([
            'view_dashboard',
            'view_document',
            'approve_document',
            'view_reports',
            'view_audit_logs',
        ]);

        // 3. ManajemenEksekutif - dashboard access and document management
        if (!Role::where('name', 'ManajemenEksekutif')->exists()) {
            $role = Role::create(['name' => 'ManajemenEksekutif']);
        } else {
            $role = Role::where('name', 'ManajemenEksekutif')->first();
        }
        $role->syncPermissions([
            'view_dashboard',
            'create_document',
            'view_document',
            'edit_document',
            'view_reports',
            'generate_reports',
        ]);

        // 4. ManajemenOperasional - dashboard access and daily operations
        if (!Role::where('name', 'ManajemenOperasional')->exists()) {
            $role = Role::create(['name' => 'ManajemenOperasional']);
        } else {
            $role = Role::where('name', 'ManajemenOperasional')->first();
        }
        $role->syncPermissions([
            'view_dashboard',
            'create_document',
            'view_document',
            'edit_document',
            'view_reports',
        ]);

        // 5. Staf - basic access
        if (!Role::where('name', 'Staf')->exists()) {
            $role = Role::create(['name' => 'Staf']);
        } else {
            $role = Role::where('name', 'Staf')->first();
        }
        $role->syncPermissions([
            'view_dashboard',
            'view_document',
            'create_document',
        ]);

        // 6. TenantAdmin - administrator rumah sakit
        if (!Role::where('name', 'TenantAdmin')->exists()) {
            $role = Role::create(['name' => 'TenantAdmin']);
        } else {
            $role = Role::where('name', 'TenantAdmin')->first();
        }
        $role->syncPermissions([
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
        ]);
    }
}
