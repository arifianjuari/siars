<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SnarsVersion;
use App\Models\SnarsUpdate;
use App\Models\User;
use Illuminate\Support\Str;

class SnarsVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error('No users found in the database. Please run UserSeeder first.');
            return;
        }

        // Check if versions already exist and create them if not
        $version1 = SnarsVersion::where('version_number', '1.0')->first();
        if (!$version1) {
            $version1 = SnarsVersion::create([
                'id' => Str::uuid(),
                'version_number' => '1.0',
                'name' => 'SNARS Edisi 1',
                'description' => 'Standar Nasional Akreditasi Rumah Sakit Edisi 1',
                'release_date' => '2022-01-01',
                'effective_date' => '2022-01-15',
                'is_active' => true,
                'created_by' => $user->id,
            ]);
            $this->command->info('Version 1.0 created successfully!');
        } else {
            $this->command->info('Version 1.0 already exists, skipping creation.');
        }

        $version2 = SnarsVersion::where('version_number', '1.1')->first();
        if (!$version2) {
            $version2 = SnarsVersion::create([
                'id' => Str::uuid(),
                'version_number' => '1.1',
                'name' => 'SNARS Edisi 1 Revisi 1',
                'description' => 'Standar Nasional Akreditasi Rumah Sakit Edisi 1 Revisi 1',
                'release_date' => '2023-01-01',
                'effective_date' => '2023-02-01',
                'is_active' => true,
                'created_by' => $user->id,
            ]);
            $this->command->info('Version 1.1 created successfully!');
        } else {
            $this->command->info('Version 1.1 already exists, skipping creation.');
        }

        // Create SNARS Updates if they don't exist
        if ($version1) {
            $update1 = SnarsUpdate::where('version_id', $version1->id)
                ->where('title', 'Pembaruan Standar Pelayanan Pasien')
                ->first();
                
            if (!$update1) {
                SnarsUpdate::create([
                    'id' => Str::uuid(),
                    'version_id' => $version1->id,
                    'update_type' => 'major',
                    'title' => 'Pembaruan Standar Pelayanan Pasien',
                    'description' => 'Penambahan standar baru untuk pelayanan pasien',
                    'changes' => 'Menambahkan 5 elemen penilaian baru pada standar pelayanan pasien',
                    'update_date' => '2022-02-01',
                    'is_published' => true,
                    'created_by' => $user->id,
                ]);
                $this->command->info('Update for Version 1.0 created successfully!');
            } else {
                $this->command->info('Update for Version 1.0 already exists, skipping creation.');
            }
        }

        if ($version2) {
            $update2 = SnarsUpdate::where('version_id', $version2->id)
                ->where('title', 'Revisi Redaksional')
                ->first();
                
            if (!$update2) {
                SnarsUpdate::create([
                    'id' => Str::uuid(),
                    'version_id' => $version2->id,
                    'update_type' => 'minor',
                    'title' => 'Revisi Redaksional',
                    'description' => 'Perbaikan redaksional pada standar pelayanan pasien',
                    'changes' => 'Memperbaiki tata bahasa dan kejelasan pada 3 elemen penilaian',
                    'update_date' => '2023-02-01',
                    'is_published' => true,
                    'created_by' => $user->id,
                ]);
                $this->command->info('Update for Version 1.1 created successfully!');
            } else {
                $this->command->info('Update for Version 1.1 already exists, skipping creation.');
            }
        }

        $this->command->info('SNARS versions and updates seeded successfully!');
    }
}
