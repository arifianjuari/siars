<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat tenant default untuk RSUD
        Tenant::create([
            'id' => Str::uuid()->toString(),
            'name' => 'RSUD Contoh',
            'code' => 'RSUD01',
            'address' => 'Jl. Rumah Sakit No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12345',
            'phone' => '021-12345678',
            'email' => 'info@rsudcontoh.com',
            'website' => 'https://rsudcontoh.com',
            'description' => 'Rumah Sakit Umum Daerah Contoh',
            'is_active' => true,
        ]);
    }
}
