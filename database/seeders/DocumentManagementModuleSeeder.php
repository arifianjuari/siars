<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah modul sudah ada
        $moduleExists = Module::where('code', 'document_management')->exists();

        if (!$moduleExists) {
            // Buat modul Manajemen Dokumen
            DB::table('modules')->insert([
                'name' => 'Manajemen Dokumen',
                'code' => 'document_management',
                'slug' => 'document-management',
                'description' => 'Modul untuk mengelola dokumen internal rumah sakit seperti nota dinas, undangan rapat, dan notulensi rapat',
                'icon' => 'bi bi-file-text',
                'order' => 3, // Sesuaikan dengan urutan yang diinginkan
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mendapatkan ID modul
            $moduleId = Module::where('code', 'document_management')->first()->id;

            // Tambahkan permission untuk roles
            $roles = DB::table('roles')->get();

            foreach ($roles as $role) {
                if ($role->name === 'superadmin') {
                    // Superadmin memiliki semua permission
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module_id' => $moduleId,
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_approve' => true,
                        'can_activate' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } elseif ($role->name === 'tenant_admin') {
                    // Tenant admin memiliki semua permission kecuali aktivasi
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module_id' => $moduleId,
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_approve' => true,
                        'can_activate' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Role lain hanya dapat melihat
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module_id' => $moduleId,
                        'can_view' => true,
                        'can_create' => false,
                        'can_edit' => false,
                        'can_delete' => false,
                        'can_approve' => false,
                        'can_activate' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $this->command->info('Modul Manajemen Dokumen berhasil ditambahkan');
        } else {
            $this->command->info('Modul Manajemen Dokumen sudah ada');
        }
    }
}
