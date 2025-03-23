<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\TenantSeeder;
use Database\Seeders\ModuleSeeder;
use Database\Seeders\SuperadminTenantAccessSeeder;
use Database\Seeders\RiskManagementModuleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            TenantSeeder::class,
            ModuleSeeder::class,
            RiskManagementModuleSeeder::class,
            UserSeeder::class,
            SuperadminTenantAccessSeeder::class,
        ]);
    }
}
