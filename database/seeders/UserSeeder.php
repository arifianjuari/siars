<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users for each role with standard password
        $defaultPassword = Hash::make('password123');

        // 1. Superadmin
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@siars.com',
            'password' => $defaultPassword,
            'employee_id' => 'EMP001',
            'phone' => '081234567890',
            'position' => 'Administrator Sistem',
            'department' => 'IT',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $user->assignRole('Superadmin');

        // 2. Manajemen Strategis
        $user = User::create([
            'name' => 'Direktur Utama',
            'email' => 'direktur@siars.com',
            'password' => $defaultPassword,
            'employee_id' => 'EMP002',
            'phone' => '081234567891',
            'position' => 'Direktur Utama',
            'department' => 'Direksi',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $user->assignRole('ManajemenStrategis');

        // 3. Manajemen Eksekutif
        $user = User::create([
            'name' => 'Manajer Mutu',
            'email' => 'mutu@siars.com',
            'password' => $defaultPassword,
            'employee_id' => 'EMP003',
            'phone' => '081234567892',
            'position' => 'Manajer Mutu',
            'department' => 'Komite Mutu',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $user->assignRole('ManajemenEksekutif');

        // 4. Manajemen Operasional
        $user = User::create([
            'name' => 'Kepala Bagian',
            'email' => 'kabag@siars.com',
            'password' => $defaultPassword,
            'employee_id' => 'EMP004',
            'phone' => '081234567893',
            'position' => 'Kepala Bagian',
            'department' => 'Pelayanan Medis',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $user->assignRole('ManajemenOperasional');

        // 5. Staf
        $user = User::create([
            'name' => 'Staf Akreditasi',
            'email' => 'staf@siars.com',
            'password' => $defaultPassword,
            'employee_id' => 'EMP005',
            'phone' => '081234567894',
            'position' => 'Staf',
            'department' => 'Akreditasi',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $user->assignRole('Staf');
    }
}
