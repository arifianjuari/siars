<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\IncidentSubtype;
use App\Models\Location;
use App\Models\RiskClassification;
use Carbon\Carbon;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menambahkan jenis-jenis insiden
        $incidentTypes = [
            ['name' => 'Kesalahan Pemberian Obat', 'code' => 'KPO', 'description' => 'Kesalahan dalam proses pemberian obat kepada pasien'],
            ['name' => 'Pasien Jatuh', 'code' => 'PJ', 'description' => 'Insiden pasien terjatuh dari tempat tidur atau saat mobilisasi'],
            ['name' => 'Kesalahan Identifikasi', 'code' => 'KI', 'description' => 'Kesalahan dalam identifikasi pasien'],
            ['name' => 'Kesalahan Tindakan', 'code' => 'KT', 'description' => 'Kesalahan dalam prosedur tindakan medis'],
            ['name' => 'Infeksi Terkait Layanan', 'code' => 'ITL', 'description' => 'Infeksi yang terjadi selama atau setelah perawatan'],
        ];

        foreach ($incidentTypes as $type) {
            IncidentType::create($type);
        }

        // Menambahkan lokasi insiden
        $locations = [
            ['name' => 'Rawat Inap Lantai 1', 'code' => 'RI-1', 'description' => 'Ruang rawat inap lantai 1'],
            ['name' => 'Rawat Inap Lantai 2', 'code' => 'RI-2', 'description' => 'Ruang rawat inap lantai 2'],
            ['name' => 'Instalasi Gawat Darurat', 'code' => 'IGD', 'description' => 'Instalasi Gawat Darurat'],
            ['name' => 'Poliklinik', 'code' => 'PK', 'description' => 'Poliklinik rawat jalan'],
            ['name' => 'ICU', 'code' => 'ICU', 'description' => 'Intensive Care Unit'],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }

        // Menambahkan data insiden
        $incidents = [
            [
                'tanggal_waktu_kejadian' => Carbon::now()->subDays(7),
                'location_id' => 1,
                'incident_type_id' => 1,
                'nama_pasien' => 'Ahmad Roni',
                'no_rm' => '202403001',
                'kronologis' => 'Pasien diberikan obat yang salah oleh perawat jaga malam.',
                'reporter_id' => 1,
                'status' => 'Baru',
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'tanggal_waktu_kejadian' => Carbon::now()->subDays(5),
                'location_id' => 3,
                'incident_type_id' => 2,
                'nama_pasien' => 'Sinta Dewi',
                'no_rm' => '202403002',
                'kronologis' => 'Pasien terjatuh dari tempat tidur saat hendak ke kamar mandi tanpa didampingi keluarga.',
                'reporter_id' => 1,
                'status' => 'Proses',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'tanggal_waktu_kejadian' => Carbon::now()->subDays(3),
                'location_id' => 2,
                'incident_type_id' => 3,
                'nama_pasien' => 'Budi Santoso',
                'no_rm' => '202403003',
                'kronologis' => 'Pasien diberikan diet untuk pasien lain karena kesalahan identifikasi.',
                'reporter_id' => 1,
                'status' => 'Evaluasi',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'tanggal_waktu_kejadian' => Carbon::now()->subDays(14),
                'location_id' => 5,
                'incident_type_id' => 4,
                'nama_pasien' => 'Joko Widodo',
                'no_rm' => '202403004',
                'kronologis' => 'Pasien diberikan tindakan yang seharusnya untuk pasien lain.',
                'reporter_id' => 1,
                'status' => 'Selesai',
                'created_at' => Carbon::now()->subDays(14),
            ],
            [
                'tanggal_waktu_kejadian' => Carbon::now()->subDays(2),
                'location_id' => 4,
                'incident_type_id' => 5,
                'nama_pasien' => 'Anisa Rahma',
                'no_rm' => '202403005',
                'kronologis' => 'Pasien mengalami infeksi setelah pemasangan kateter.',
                'reporter_id' => 1,
                'status' => 'Baru',
                'created_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($incidents as $incident) {
            Incident::create($incident);
        }

        // Menambahkan data klasifikasi risiko
        $classifications = [
            [
                'incident_id' => 1,
                'dampak' => 3,
                'dampak_detail' => 'Menyebabkan peningkatan observasi pasien',
                'probabilitas' => 3,
                'probabilitas_detail' => 'Mungkin terjadi (antara 10-50%)',
                'skor_risiko' => 9,
                'level_risiko' => 'Tinggi',
                'zona_risiko' => 'Merah',
                'classifier_id' => 1,
                'created_at' => Carbon::now()->subDays(6),
            ],
            [
                'incident_id' => 2,
                'dampak' => 3,
                'dampak_detail' => 'Menyebabkan cedera ringan pada pasien',
                'probabilitas' => 4,
                'probabilitas_detail' => 'Akan terjadi (antara 50-90%)',
                'skor_risiko' => 12,
                'level_risiko' => 'Tinggi',
                'zona_risiko' => 'Merah',
                'classifier_id' => 1,
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'incident_id' => 3,
                'dampak' => 2,
                'dampak_detail' => 'Menyebabkan efek minimal',
                'probabilitas' => 3,
                'probabilitas_detail' => 'Mungkin terjadi (antara 10-50%)',
                'skor_risiko' => 6,
                'level_risiko' => 'Sedang',
                'zona_risiko' => 'Kuning',
                'classifier_id' => 1,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'incident_id' => 4,
                'dampak' => 2,
                'dampak_detail' => 'Menyebabkan efek minimal',
                'probabilitas' => 2,
                'probabilitas_detail' => 'Tidak mungkin terjadi (antara 1-10%)',
                'skor_risiko' => 4,
                'level_risiko' => 'Rendah',
                'zona_risiko' => 'Hijau',
                'classifier_id' => 1,
                'created_at' => Carbon::now()->subDays(13),
            ],
        ];

        foreach ($classifications as $classification) {
            RiskClassification::create($classification);
        }
    }
}
