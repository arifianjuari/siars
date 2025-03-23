<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SnarsAssessmentElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID bab dari database
        $chapters = DB::table('snars_chapters')->get();
        
        if ($chapters->isEmpty()) {
            $this->command->info('Tidak ada data bab SNARS. Silakan jalankan migrasi untuk membuat bab terlebih dahulu.');
            return;
        }
        
        $elements = [];
        
        // Elemen penilaian untuk bab APK (Asesmen Pasien dan Keluarga)
        $apkChapter = $chapters->where('code', 'APK')->first();
        if ($apkChapter) {
            $elements = array_merge($elements, [
                [
                    'id' => Str::uuid()->toString(),
                    'code' => 'APK.1.1',
                    'description' => 'RS menetapkan regulasi asesmen awal medis dan keperawatan.',
                    'assessment_guide' => 'Periksa dokumen regulasi asesmen awal medis dan keperawatan.',
                    'order' => 1,
                    'is_active' => true,
                    'compliance_status' => 'not_assessed',
                    'chapter_id' => $apkChapter->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'code' => 'APK.1.2',
                    'description' => 'Asesmen awal medis dan keperawatan dilaksanakan dalam waktu 24 jam sejak pasien masuk rawat inap.',
                    'assessment_guide' => 'Periksa rekam medis pasien rawat inap untuk memastikan asesmen dilakukan dalam 24 jam.',
                    'order' => 2,
                    'is_active' => true,
                    'compliance_status' => 'not_assessed',
                    'chapter_id' => $apkChapter->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'code' => 'APK.1.3',
                    'description' => 'Asesmen dilakukan oleh tenaga yang kompeten.',
                    'assessment_guide' => 'Periksa dokumen kredensial staf yang melakukan asesmen.',
                    'order' => 3,
                    'is_active' => true,
                    'compliance_status' => 'not_assessed',
                    'chapter_id' => $apkChapter->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
        
        // Elemen penilaian untuk bab ARK (Akses ke Rumah Sakit dan Kontinuitas Pelayanan)
        $arkChapter = $chapters->where('code', 'ARK')->first();
        if ($arkChapter) {
            $elements = array_merge($elements, [
                [
                    'id' => Str::uuid()->toString(),
                    'code' => 'ARK.1.1',
                    'description' => 'RS menetapkan regulasi tentang penerimaan pasien di RS.',
                    'assessment_guide' => 'Periksa dokumen regulasi penerimaan pasien.',
                    'order' => 1,
                    'is_active' => true,
                    'compliance_status' => 'not_assessed',
                    'chapter_id' => $arkChapter->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'code' => 'ARK.1.2',
                    'description' => 'RS menetapkan regulasi tentang transfer pasien antar unit pelayanan di RS.',
                    'assessment_guide' => 'Periksa dokumen regulasi transfer pasien antar unit pelayanan.',
                    'order' => 2,
                    'is_active' => true,
                    'compliance_status' => 'not_assessed',
                    'chapter_id' => $arkChapter->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
        
        // Elemen penilaian untuk bab PAP (Pendidikan Pasien dan Keluarga)
        $papChapter = $chapters->where('code', 'PAP')->first();
        if ($papChapter) {
            $elements = array_merge($elements, [
                [
                    'id' => Str::uuid()->toString(),
                    'code' => 'PAP.1.1',
                    'description' => 'RS menetapkan regulasi tentang pendidikan pasien dan keluarga.',
                    'assessment_guide' => 'Periksa dokumen regulasi pendidikan pasien dan keluarga.',
                    'order' => 1,
                    'is_active' => true,
                    'compliance_status' => 'not_assessed',
                    'chapter_id' => $papChapter->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'code' => 'PAP.1.2',
                    'description' => 'Pendidikan diberikan kepada pasien dan keluarga sesuai dengan kebutuhan pasien.',
                    'assessment_guide' => 'Periksa dokumentasi pendidikan pasien dan keluarga di rekam medis.',
                    'order' => 2,
                    'is_active' => true,
                    'compliance_status' => 'not_assessed',
                    'chapter_id' => $papChapter->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
        
        // Insert data elemen penilaian
        DB::table('snars_assessment_elements')->insert($elements);
    }
}
