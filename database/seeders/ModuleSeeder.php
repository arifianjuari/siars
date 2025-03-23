<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah modul SNARS sudah ada
        if (!DB::table('modules')->where('code', 'SNARS')->exists()) {
            // 1. Modul SNARS (Core/Inti)
            DB::table('modules')->insert([
                'name' => 'SNARS',
                'code' => 'SNARS',
                'slug' => 'snars',
                'description' => 'Modul untuk manajemen akreditasi SNARS',
                'icon' => 'fas fa-check-circle',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Modul SNARS berhasil ditambahkan');
        }

        // Cek apakah modul Dokumen sudah ada
        if (!DB::table('modules')->where('code', 'DOC')->exists()) {
            // 2. Modul Dokumen
            DB::table('modules')->insert([
                'name' => 'Manajemen Dokumen',
                'code' => 'DOC',
                'slug' => 'documents',
                'description' => 'Modul untuk manajemen dokumen rumah sakit',
                'icon' => 'fas fa-file-alt',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Modul Dokumen berhasil ditambahkan');
        }

        // Cek apakah modul Indikator Mutu sudah ada
        if (!DB::table('modules')->where('code', 'QI')->exists()) {
            // 3. Modul Indikator Mutu
            DB::table('modules')->insert([
                'name' => 'Indikator Mutu',
                'code' => 'QI',
                'slug' => 'quality-indicators',
                'description' => 'Modul untuk pengelolaan indikator mutu rumah sakit',
                'icon' => 'fas fa-chart-line',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Modul Indikator Mutu berhasil ditambahkan');
        }
    }
}
