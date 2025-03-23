<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah modul Manajemen Risiko sudah ada
        if (!DB::table('modules')->where('code', 'RISK')->exists()) {
            // Modul Manajemen Risiko
            DB::table('modules')->insert([
                'name' => 'Manajemen Risiko',
                'code' => 'RISK',
                'slug' => 'risk-management',
                'description' => 'Modul untuk manajemen risiko rumah sakit',
                'icon' => 'fas fa-exclamation-triangle',
                'order' => 4, // Sesuaikan dengan urutan yang Anda inginkan
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Modul Manajemen Risiko berhasil ditambahkan');
        } else {
            $this->command->info('Modul Manajemen Risiko sudah ada');
        }
    }
}
