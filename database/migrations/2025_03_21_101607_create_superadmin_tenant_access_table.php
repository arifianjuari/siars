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
        Schema::create('superadmin_tenant_access', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->uuid('tenant_id');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->primary(['user_id', 'tenant_id']);

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });

        // Berikan akses ke semua superadmin untuk semua tenant yang ada
        $superadminRole = Role::where('name', 'Superadmin')->first();

        if ($superadminRole) {
            $superadmins = DB::table('model_has_roles')
                ->where('role_id', $superadminRole->id)
                ->where('model_type', 'App\\Models\\User')
                ->pluck('model_id');

            $tenants = DB::table('tenants')->pluck('id');

            if ($superadmins->count() > 0 && $tenants->count() > 0) {
                $now = now();
                $data = [];

                foreach ($superadmins as $userId) {
                    $isFirstTenant = true;

                    foreach ($tenants as $tenantId) {
                        $data[] = [
                            'user_id' => $userId,
                            'tenant_id' => $tenantId,
                            'is_default' => $isFirstTenant, // Tenant pertama menjadi default
                            'created_at' => $now,
                            'updated_at' => $now
                        ];

                        $isFirstTenant = false;
                    }
                }

                if (!empty($data)) {
                    DB::table('superadmin_tenant_access')->insert($data);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('superadmin_tenant_access');
    }
};
