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
        Schema::create('role_management_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_role_id');
            $table->unsignedBigInteger('manageable_role_id');
            $table->timestamps();

            $table->primary(['manager_role_id', 'manageable_role_id']);

            $table->foreign('manager_role_id')->references('id')->on('roles');
            $table->foreign('manageable_role_id')->references('id')->on('roles');
        });

        // Sekarang kita tentukan hierarki pengelolaan role
        $roleNames = [
            'Superadmin',
            'ManajemenStrategis',
            'ManajemenEksekutif',
            'ManajemenOperasional',
            'Staf',
            'TenantAdmin'
        ];

        $roles = Role::whereIn('name', $roleNames)->get()->keyBy('name');

        // Hanya lanjutkan jika semua role ditemukan
        if (count($roles) === count($roleNames)) {
            $hierarchies = [
                // Superadmin dapat mengelola semua role
                ['manager' => 'Superadmin', 'manageable' => 'Superadmin'],
                ['manager' => 'Superadmin', 'manageable' => 'ManajemenStrategis'],
                ['manager' => 'Superadmin', 'manageable' => 'ManajemenEksekutif'],
                ['manager' => 'Superadmin', 'manageable' => 'ManajemenOperasional'],
                ['manager' => 'Superadmin', 'manageable' => 'Staf'],
                ['manager' => 'Superadmin', 'manageable' => 'TenantAdmin'],

                // TenantAdmin dapat mengelola semua role kecuali Superadmin dan TenantAdmin lain
                ['manager' => 'TenantAdmin', 'manageable' => 'ManajemenStrategis'],
                ['manager' => 'TenantAdmin', 'manageable' => 'ManajemenEksekutif'],
                ['manager' => 'TenantAdmin', 'manageable' => 'ManajemenOperasional'],
                ['manager' => 'TenantAdmin', 'manageable' => 'Staf'],

                // ManajemenStrategis dapat mengelola role di bawahnya
                ['manager' => 'ManajemenStrategis', 'manageable' => 'ManajemenEksekutif'],
                ['manager' => 'ManajemenStrategis', 'manageable' => 'ManajemenOperasional'],
                ['manager' => 'ManajemenStrategis', 'manageable' => 'Staf'],

                // ManajemenEksekutif dapat mengelola role operasional dan staf
                ['manager' => 'ManajemenEksekutif', 'manageable' => 'ManajemenOperasional'],
                ['manager' => 'ManajemenEksekutif', 'manageable' => 'Staf'],

                // ManajemenOperasional dapat mengelola staf saja
                ['manager' => 'ManajemenOperasional', 'manageable' => 'Staf'],
            ];

            $now = now();
            $data = [];

            foreach ($hierarchies as $hierarchy) {
                if (isset($roles[$hierarchy['manager']]) && isset($roles[$hierarchy['manageable']])) {
                    $data[] = [
                        'manager_role_id' => $roles[$hierarchy['manager']]->id,
                        'manageable_role_id' => $roles[$hierarchy['manageable']]->id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }

            if (!empty($data)) {
                DB::table('role_management_permissions')->insert($data);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_management_permissions');
    }
};
