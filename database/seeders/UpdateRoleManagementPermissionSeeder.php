<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class UpdateRoleManagementPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek dan buat tabel role_management_permissions jika belum ada
        if (!Schema::hasTable('role_management_permissions')) {
            Schema::create('role_management_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('manager_role_id');
                $table->unsignedBigInteger('manageable_role_id');
                $table->timestamps();

                $table->unique(['manager_role_id', 'manageable_role_id'], 'role_mgmt_perms_unique');

                $table->foreign('manager_role_id', 'role_mgmt_perms_manager_fk')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->foreign('manageable_role_id', 'role_mgmt_perms_manageable_fk')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');
            });

            $this->command->info('Tabel role_management_permissions berhasil dibuat.');
        }

        // Cek dan buat tabel role_module_permissions jika belum ada
        if (!Schema::hasTable('role_module_permissions')) {
            Schema::create('role_module_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('module_id');
                $table->boolean('can_view')->default(false);
                $table->boolean('can_create')->default(false);
                $table->boolean('can_edit')->default(false);
                $table->boolean('can_delete')->default(false);
                $table->boolean('can_approve')->default(false);
                $table->boolean('can_activate')->default(false);
                $table->timestamps();

                $table->unique(['role_id', 'module_id'], 'role_module_perms_unique');

                $table->foreign('role_id', 'role_module_perms_role_fk')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->foreign('module_id', 'role_module_perms_module_fk')
                    ->references('id')
                    ->on('modules')
                    ->onDelete('cascade');
            });

            $this->command->info('Tabel role_module_permissions berhasil dibuat.');
        }

        // Menambahkan relasi antara TenantAdmin dan role lainnya
        $hierarchies = [
            // TenantAdmin dapat mengelola semua role kecuali Superadmin dan TenantAdmin lain
            ['manager' => 'TenantAdmin', 'manageable' => 'ManajemenStrategis'],
            ['manager' => 'TenantAdmin', 'manageable' => 'ManajemenEksekutif'],
            ['manager' => 'TenantAdmin', 'manageable' => 'ManajemenOperasional'],
            ['manager' => 'TenantAdmin', 'manageable' => 'Staf'],

            // Superadmin dapat mengelola semua role
            ['manager' => 'Superadmin', 'manageable' => 'TenantAdmin'],
            ['manager' => 'Superadmin', 'manageable' => 'ManajemenStrategis'],
            ['manager' => 'Superadmin', 'manageable' => 'ManajemenEksekutif'],
            ['manager' => 'Superadmin', 'manageable' => 'ManajemenOperasional'],
            ['manager' => 'Superadmin', 'manageable' => 'Staf'],
        ];

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

                    $this->command->info("Menambahkan relasi: {$hierarchy['manager']} dapat mengelola {$hierarchy['manageable']}");
                }
            } else {
                if (!$managerRole) {
                    $this->command->error("Role {$hierarchy['manager']} tidak ditemukan");
                }
                if (!$manageableRole) {
                    $this->command->error("Role {$hierarchy['manageable']} tidak ditemukan");
                }
            }
        }

        if (!empty($data)) {
            DB::table('role_management_permissions')->insert($data);
            $this->command->info('Relasi manajemen role berhasil ditambahkan.');
        } else {
            $this->command->info('Tidak ada relasi baru yang perlu ditambahkan.');
        }

        // Tambahkan izin modul untuk TenantAdmin
        $tenantAdminRole = Role::where('name', 'TenantAdmin')->first();
        if ($tenantAdminRole && Schema::hasTable('role_module_permissions') && Schema::hasTable('modules')) {
            $modules = DB::table('modules')->get();
            $modulePermissions = [];

            foreach ($modules as $module) {
                // Cek apakah izin sudah ada
                $exists = DB::table('role_module_permissions')
                    ->where('role_id', $tenantAdminRole->id)
                    ->where('module_id', $module->id)
                    ->exists();

                if (!$exists) {
                    $modulePermissions[] = [
                        'role_id' => $tenantAdminRole->id,
                        'module_id' => $module->id,
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_approve' => true,
                        'can_activate' => false, // Hanya superadmin yang bisa mengaktifkan modul
                        'created_at' => $now,
                        'updated_at' => $now
                    ];

                    $this->command->info("Menambahkan izin modul untuk TenantAdmin: {$module->name}");
                }
            }

            if (!empty($modulePermissions)) {
                DB::table('role_module_permissions')->insert($modulePermissions);
                $this->command->info('Izin modul untuk TenantAdmin berhasil ditambahkan.');
            } else {
                $this->command->info('Semua izin modul untuk TenantAdmin sudah ada.');
            }
        }

        // Tambahkan izin modul Manajemen Risiko untuk Superadmin
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            $riskModule = DB::table('modules')->where('code', 'RISK')->first();

            if ($riskModule) {
                $exists = DB::table('role_module_permissions')
                    ->where('role_id', $superAdminRole->id)
                    ->where('module_id', $riskModule->id)
                    ->exists();

                if (!$exists) {
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $superAdminRole->id,
                        'module_id' => $riskModule->id,
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_approve' => true,
                        'can_activate' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $this->command->info('Izin modul Manajemen Risiko untuk Superadmin berhasil ditambahkan.');
                }
            }
        }
    }
}
