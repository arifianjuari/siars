<?php

namespace Database\Seeders;

use App\Models\RiskCategory;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class RiskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID tenant pertama
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->error('Tidak ada tenant yang ditemukan. Silakan buat tenant terlebih dahulu.');
            return;
        }

        $tenantId = $tenant->id;

        // Kategori utama
        $mainCategories = [
            [
                'name' => 'Klinis',
                'code' => 'K',
                'description' => 'Risiko terkait pelayanan klinis dan keselamatan pasien',
                'is_active' => true,
                'order' => 1,
                'tenant_id' => $tenantId,
            ],
            [
                'name' => 'Non-Klinis',
                'code' => 'NK',
                'description' => 'Risiko terkait proses non-klinis dan operasional',
                'is_active' => true,
                'order' => 2,
                'tenant_id' => $tenantId,
            ],
            [
                'name' => 'Keuangan',
                'code' => 'KEU',
                'description' => 'Risiko terkait aspek keuangan dan pengelolaan dana',
                'is_active' => true,
                'order' => 3,
                'tenant_id' => $tenantId,
            ],
            [
                'name' => 'Strategis',
                'code' => 'STR',
                'description' => 'Risiko terkait pencapaian tujuan strategis organisasi',
                'is_active' => true,
                'order' => 4,
                'tenant_id' => $tenantId,
            ],
        ];

        foreach ($mainCategories as $category) {
            RiskCategory::create($category);
        }

        // Subkategori untuk kategori Klinis
        $klinisId = RiskCategory::where('code', 'K')->where('tenant_id', $tenantId)->first()->id;
        $klinisSubCategories = [
            [
                'name' => 'Keselamatan Pasien',
                'code' => 'K-KP',
                'description' => 'Risiko terkait keselamatan pasien',
                'parent_id' => $klinisId,
                'is_active' => true,
                'order' => 1,
                'tenant_id' => $tenantId,
            ],
            [
                'name' => 'Infeksi Terkait Pelayanan',
                'code' => 'K-ITP',
                'description' => 'Risiko terkait infeksi yang didapat di rumah sakit',
                'parent_id' => $klinisId,
                'is_active' => true,
                'order' => 2,
                'tenant_id' => $tenantId,
            ],
            [
                'name' => 'Kesalahan Pengobatan',
                'code' => 'K-KO',
                'description' => 'Risiko terkait kesalahan dalam pemberian obat',
                'parent_id' => $klinisId,
                'is_active' => true,
                'order' => 3,
                'tenant_id' => $tenantId,
            ],
        ];

        foreach ($klinisSubCategories as $category) {
            RiskCategory::create($category);
        }

        // Subkategori untuk kategori Non-Klinis
        $nonKlinisId = RiskCategory::where('code', 'NK')->where('tenant_id', $tenantId)->first()->id;
        $nonKlinisSubCategories = [
            [
                'name' => 'Fasilitas & Infrastruktur',
                'code' => 'NK-FI',
                'description' => 'Risiko terkait fasilitas dan infrastruktur rumah sakit',
                'parent_id' => $nonKlinisId,
                'is_active' => true,
                'order' => 1,
                'tenant_id' => $tenantId,
            ],
            [
                'name' => 'Keselamatan Kerja',
                'code' => 'NK-KK',
                'description' => 'Risiko terkait keselamatan pekerja',
                'parent_id' => $nonKlinisId,
                'is_active' => true,
                'order' => 2,
                'tenant_id' => $tenantId,
            ],
            [
                'name' => 'Lingkungan',
                'code' => 'NK-LK',
                'description' => 'Risiko terkait dampak lingkungan',
                'parent_id' => $nonKlinisId,
                'is_active' => true,
                'order' => 3,
                'tenant_id' => $tenantId,
            ],
        ];

        foreach ($nonKlinisSubCategories as $category) {
            RiskCategory::create($category);
        }
    }
}
