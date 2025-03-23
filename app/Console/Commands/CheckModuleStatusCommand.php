<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckModuleStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:module-status {--module-id= : ID modul spesifik yang ingin diperiksa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memeriksa status modul untuk semua tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleId = $this->option('module-id');

        if ($moduleId) {
            $this->info("Memeriksa status modul ID $moduleId untuk semua tenant...");
            $query = DB::table('tenant_modules')->where('module_id', $moduleId);
        } else {
            $this->info("Memeriksa status semua modul untuk semua tenant...");
            $query = DB::table('tenant_modules');
        }

        $tenantModules = $query->get();

        if ($tenantModules->isEmpty()) {
            $this->error('Tidak ditemukan entri modul untuk tenant manapun!');
            return;
        }

        // Dapatkan informasi modul
        $modules = DB::table('modules')->pluck('name', 'id')->toArray();

        $this->info('Total relasi tenant-modul: ' . $tenantModules->count());

        $this->table(
            ['Tenant ID', 'Module ID', 'Nama Modul', 'Is Active', 'Approved By', 'Approved At'],
            $tenantModules->map(function ($item) use ($modules) {
                $moduleName = $modules[$item->module_id] ?? "Modul #" . $item->module_id;
                return [
                    'tenant_id' => $item->tenant_id,
                    'module_id' => $item->module_id,
                    'module_name' => $moduleName,
                    'is_active' => $item->is_active ? 'Ya' : 'Tidak',
                    'approved_by' => $item->approved_by ?? 'NULL',
                    'approved_at' => $item->approved_at ?? 'NULL'
                ];
            })
        );

        $activeCount = $tenantModules->where('is_active', 1)->count();
        $inactiveCount = $tenantModules->where('is_active', 0)->count();

        $this->info('Jumlah relasi tenant-modul aktif: ' . $activeCount);
        $this->info('Jumlah relasi tenant-modul tidak aktif: ' . $inactiveCount);

        // Tampilkan ringkasan per modul
        if (!$moduleId) {
            $this->info("\nRingkasan Status Per Modul:");

            $moduleStats = [];
            foreach ($tenantModules as $item) {
                $moduleId = $item->module_id;
                if (!isset($moduleStats[$moduleId])) {
                    $moduleStats[$moduleId] = [
                        'module_id' => $moduleId,
                        'module_name' => $modules[$moduleId] ?? "Modul #" . $moduleId,
                        'total' => 0,
                        'active' => 0,
                        'inactive' => 0
                    ];
                }

                $moduleStats[$moduleId]['total']++;
                if ($item->is_active) {
                    $moduleStats[$moduleId]['active']++;
                } else {
                    $moduleStats[$moduleId]['inactive']++;
                }
            }

            $this->table(
                ['Module ID', 'Nama Modul', 'Total', 'Aktif', 'Tidak Aktif'],
                collect($moduleStats)->map(function ($stat) {
                    return [
                        'module_id' => $stat['module_id'],
                        'module_name' => $stat['module_name'],
                        'total' => $stat['total'],
                        'active' => $stat['active'],
                        'inactive' => $stat['inactive']
                    ];
                })
            );
        }
    }
}
