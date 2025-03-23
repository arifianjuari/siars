<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ambil semua superadmin
        $superadmins = User::role('Superadmin')->get();

        // Ambil semua tenant yang aktif
        $activeTenants = Tenant::where('is_active', true)->get();

        foreach ($superadmins as $admin) {
            foreach ($activeTenants as $tenant) {
                // Periksa apakah relasi sudah ada
                $exists = DB::table('superadmin_tenant_access')
                    ->where('user_id', $admin->id)
                    ->where('tenant_id', $tenant->id)
                    ->exists();

                // Jika belum ada, tambahkan relasi
                if (!$exists) {
                    DB::table('superadmin_tenant_access')->insert([
                        'user_id' => $admin->id,
                        'tenant_id' => $tenant->id,
                        'is_default' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
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
        // Tidak perlu menghapus relasi yang sudah dibuat
    }
};
