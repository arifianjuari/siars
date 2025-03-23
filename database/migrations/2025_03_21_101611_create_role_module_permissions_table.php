<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_module_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('module_id');
            $table->boolean('can_view')->default(true);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_approve')->default(false);
            $table->boolean('can_activate')->default(false);
            $table->timestamps();

            $table->primary(['role_id', 'module_id']);

            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('module_id')->references('id')->on('modules');
        });

        // Tambahkan izin default untuk setiap role terhadap modul SNARS
        $snarsModule = DB::table('modules')->where('code', 'SNARS')->first();

        if ($snarsModule) {
            $permissions = [
                // Superadmin memiliki semua izin
                ['role_name' => 'Superadmin', 'can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true, 'can_approve' => true, 'can_activate' => true],

                // TenantAdmin memiliki semua izin kecuali can_activate
                ['role_name' => 'TenantAdmin', 'can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true, 'can_approve' => true, 'can_activate' => false],

                // ManajemenStrategis dapat melihat dan menyetujui
                ['role_name' => 'ManajemenStrategis', 'can_view' => true, 'can_create' => false, 'can_edit' => false, 'can_delete' => false, 'can_approve' => true, 'can_activate' => false],

                // ManajemenEksekutif dapat melihat, membuat, dan mengedit
                ['role_name' => 'ManajemenEksekutif', 'can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => false, 'can_approve' => false, 'can_activate' => false],

                // ManajemenOperasional dapat melihat, membuat, dan mengedit
                ['role_name' => 'ManajemenOperasional', 'can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => false, 'can_approve' => false, 'can_activate' => false],

                // Staf hanya dapat melihat dan membuat
                ['role_name' => 'Staf', 'can_view' => true, 'can_create' => true, 'can_edit' => false, 'can_delete' => false, 'can_approve' => false, 'can_activate' => false],
            ];

            $now = now();

            foreach ($permissions as $permission) {
                $role = Role::where('name', $permission['role_name'])->first();

                if ($role) {
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module_id' => $snarsModule->id,
                        'can_view' => $permission['can_view'],
                        'can_create' => $permission['can_create'],
                        'can_edit' => $permission['can_edit'],
                        'can_delete' => $permission['can_delete'],
                        'can_approve' => $permission['can_approve'],
                        'can_activate' => $permission['can_activate'],
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
        Schema::dropIfExists('role_module_permissions');
    }
};
