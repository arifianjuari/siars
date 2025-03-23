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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('tenant_id')->nullable()->after('id');
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });

        // Update existing users (kecuali superadmin) untuk memiliki tenant_id dari tenant pertama
        $superadminRole = Role::where('name', 'Superadmin')->first();
        if ($superadminRole) {
            $firstTenant = DB::table('tenants')->first();
            if ($firstTenant) {
                DB::table('users')
                    ->whereNotIn('id', function ($query) use ($superadminRole) {
                        $query->select('model_id')
                            ->from('model_has_roles')
                            ->where('role_id', $superadminRole->id)
                            ->where('model_type', 'App\\Models\\User');
                    })
                    ->update(['tenant_id' => $firstTenant->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
