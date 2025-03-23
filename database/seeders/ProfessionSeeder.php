<?php

namespace Database\Seeders;

use App\Models\Profession;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $professions = [
            ['name' => 'Dokter', 'description' => 'Dokter Umum'],
            ['name' => 'Dokter Spesialis', 'description' => 'Dokter Spesialis'],
            ['name' => 'Perawat', 'description' => 'Perawat'],
            ['name' => 'Bidan', 'description' => 'Bidan'],
            ['name' => 'Apoteker', 'description' => 'Apoteker'],
            ['name' => 'Asisten Apoteker', 'description' => 'Asisten Apoteker'],
            ['name' => 'Petugas Administrasi', 'description' => 'Petugas Administrasi'],
            ['name' => 'Petugas Keamanan', 'description' => 'Petugas Keamanan'],
            ['name' => 'Petugas Kebersihan', 'description' => 'Petugas Kebersihan'],
            ['name' => 'Petugas Gizi', 'description' => 'Petugas Gizi'],
            ['name' => 'Lainnya', 'description' => 'Profesi Lainnya']
        ];

        foreach ($professions as $profession) {
            Profession::create($profession);
        }
    }
}
