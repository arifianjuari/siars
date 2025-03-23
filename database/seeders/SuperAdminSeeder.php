<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role Superadmin exists
        $role = Role::firstOrCreate(['name' => 'Superadmin']);

        // Cek apakah sudah ada superadmin
        if (!User::whereHas('roles', function ($q) {
            $q->where('name', 'Superadmin');
        })->exists()) {
            // Create superadmin user
            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            // Assign role
            $user->assignRole('Superadmin');

            $this->command->info('User Superadmin created successfully!');
        } else {
            $this->command->info('Superadmin already exists!');
        }
    }
}
