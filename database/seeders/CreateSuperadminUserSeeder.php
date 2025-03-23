<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateSuperadminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah user superadmin sudah ada
        $superadminExists = DB::table('users')
            ->where('email', 'superadmin@siars.com')
            ->exists();

        if (!$superadminExists) {
            $this->command->info('Membuat user superadmin baru...');

            // Buat user superadmin
            $userId = DB::table('users')->insertGetId([
                'name' => 'Super Admin',
                'email' => 'superadmin@siars.com',
                'password' => Hash::make('password123'),
                'employee_id' => 'EMP001',
                'phone' => '081234567890',
                'position' => 'Administrator Sistem',
                'department' => 'IT',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Cari role Superadmin
            $superadminRole = Role::where('name', 'Superadmin')->first();

            if ($superadminRole) {
                // Assign role Superadmin ke user
                DB::table('model_has_roles')->insert([
                    'role_id' => $superadminRole->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId
                ]);

                $this->command->info('User superadmin berhasil dibuat dan role Superadmin berhasil diberikan.');
            } else {
                $this->command->error('Role Superadmin tidak ditemukan!');
            }
        } else {
            // Jika sudah ada, aktifkan user
            $user = DB::table('users')
                ->where('email', 'superadmin@siars.com')
                ->first();

            if ($user && !$user->is_active) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['is_active' => true]);

                $this->command->info('User superadmin sudah ada dan berhasil diaktifkan.');
            } else {
                $this->command->info('User superadmin sudah ada dan sudah aktif.');
            }
        }
    }
}
