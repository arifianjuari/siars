<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SnarsGroup;
use App\Models\SnarsChapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddTestChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari kelompok dengan kode K2
        $group = SnarsGroup::where('code', 'K2')->first();

        if ($group) {
            // Buat chapter baru untuk kelompok ini
            SnarsChapter::create([
                'id' => Str::uuid(),
                'snars_group_id' => $group->id,
                'code' => 'BAB1',
                'title' => 'Bab Test untuk Kelompok K2',
                'description' => 'Ini adalah bab test yang dibuat untuk menguji relasi',
                'order' => 1,
                'is_active' => true,
                'created_by' => 1, // Sesuaikan dengan ID user yang ada
                'updated_by' => 1  // Sesuaikan dengan ID user yang ada
            ]);

            $this->command->info('Chapter test berhasil ditambahkan ke Kelompok K2');
        } else {
            $this->command->error('Kelompok dengan kode K2 tidak ditemukan');

            // Cari kelompok apa saja yang ada di database
            $groups = SnarsGroup::all(['id', 'code', 'name']);

            if ($groups->count() > 0) {
                $this->command->info('Kelompok yang tersedia:');
                foreach ($groups as $availableGroup) {
                    $this->command->info(" - {$availableGroup->code}: {$availableGroup->name} (ID: {$availableGroup->id})");

                    // Pilih kelompok pertama untuk membuat bab test
                    if (!isset($selectedGroup)) {
                        $selectedGroup = $availableGroup;
                    }
                }

                if (isset($selectedGroup)) {
                    // Buat chapter baru untuk kelompok yang dipilih
                    SnarsChapter::create([
                        'id' => Str::uuid(),
                        'snars_group_id' => $selectedGroup->id,
                        'code' => 'BAB1',
                        'title' => "Bab Test untuk Kelompok {$selectedGroup->code}",
                        'description' => 'Ini adalah bab test yang dibuat untuk menguji relasi',
                        'order' => 1,
                        'is_active' => true,
                        'created_by' => 1, // Sesuaikan dengan ID user yang ada
                        'updated_by' => 1  // Sesuaikan dengan ID user yang ada
                    ]);

                    $this->command->info("Chapter test berhasil ditambahkan ke Kelompok {$selectedGroup->code}");
                }
            } else {
                $this->command->error('Tidak ada kelompok yang ditemukan di database');
            }
        }
    }
}
