<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SnarsStandard;
use App\Models\SnarsAssessmentElement;
use Illuminate\Support\Str;

class AddTestAssessmentElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari semua standard yang ada
        $standards = SnarsStandard::all();

        if ($standards->count() > 0) {
            // Pilih standard pertama untuk menambahkan elemen penilaian
            $standard = $standards->first();

            $this->command->info("Menambahkan elemen penilaian untuk Standard: {$standard->code}");

            // Buat 3 elemen penilaian untuk standard ini
            for ($i = 1; $i <= 3; $i++) {
                SnarsAssessmentElement::create([
                    'id' => Str::uuid(),
                    'standard_id' => $standard->id,
                    'code' => "EP.{$i}",
                    'description' => "Elemen Penilaian {$i} untuk Standard {$standard->code}",
                    'assessment_guide' => "Panduan penilaian untuk elemen {$i}",
                    'evidence_requirements' => "Bukti yang diperlukan untuk elemen {$i}",
                    'scoring_method' => 'binary',
                    'weight' => 1,
                    'order' => $i,
                    'is_active' => true,
                    'created_by' => 1, // Sesuaikan dengan ID user yang ada
                    'updated_by' => 1  // Sesuaikan dengan ID user yang ada
                ]);

                $this->command->info("- Elemen Penilaian EP.{$i} berhasil ditambahkan");
            }
        } else {
            $this->command->error('Tidak ada standard yang ditemukan di database');
        }
    }
}
