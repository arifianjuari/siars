<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah kolom is_active sudah ada di tabel users
        if (Schema::hasColumn('users', 'is_active')) {
            // Cari role superadmin
            $superadminRole = DB::table('roles')->where('name', 'Superadmin')->first();

            if ($superadminRole) {
                // Cari semua user dengan role superadmin
                $superadminUsers = DB::table('model_has_roles')
                    ->where('role_id', $superadminRole->id)
                    ->where('model_type', 'App\\Models\\User')
                    ->pluck('model_id');

                // Update status is_active menjadi true untuk semua superadmin
                if ($superadminUsers->count() > 0) {
                    DB::table('users')
                        ->whereIn('id', $superadminUsers)
                        ->update(['is_active' => true]);

                    // Log informasi
                    Log::info('Berhasil mengaktifkan ' . $superadminUsers->count() . ' user superadmin.');
                } else {
                    // Buat user superadmin baru jika tidak ada
                    $userId = DB::table('users')->insertGetId([
                        'name' => 'Super Admin',
                        'email' => 'superadmin@siars.com',
                        'password' => bcrypt('password123'),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Assign role Superadmin
                    DB::table('model_has_roles')->insert([
                        'role_id' => $superadminRole->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $userId,
                    ]);

                    Log::info('Membuat user superadmin baru karena tidak ditemukan.');
                }
            } else {
                Log::error('Role Superadmin tidak ditemukan.');
            }
        } else {
            Log::error('Kolom is_active tidak ditemukan di tabel users.');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu menurunkan status aktif
    }
};
