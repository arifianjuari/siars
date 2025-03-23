<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestModuleListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:modules {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test modules listing for a tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');

        if (!$tenantId) {
            $tenants = Tenant::all();
            $this->info("Available tenants:");
            foreach ($tenants as $tenant) {
                $this->line("- ID: {$tenant->id}, Name: {$tenant->name}");
            }

            $tenantId = $this->ask('Enter tenant ID to check');
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant not found");
            return 1;
        }

        $this->info("Testing module listing for tenant: {$tenant->name}");

        // Ambil modul yang aktif untuk rumah sakit
        $activatedModules = $tenant->modules()->wherePivot('is_active', true)->get();

        $this->info("Activated modules: " . $activatedModules->count());
        foreach ($activatedModules as $module) {
            $this->line("- {$module->name} (ID: {$module->id})");
        }

        // Ambil permintaan modul yang sedang pending
        $pendingRequests = $tenant->moduleActivationRequests()->where('status', 'pending')->with('module')->get();

        $this->info("\nPending activation requests: " . $pendingRequests->count());
        foreach ($pendingRequests as $request) {
            $this->line("- {$request->module->name} (ID: {$request->module->id})");
        }

        // Kumpulkan ID dari modul yang sudah aktif atau pending
        $activatedModuleIds = $activatedModules->pluck('id')->toArray();
        $pendingModuleIds = $pendingRequests->pluck('module_id')->toArray();

        $this->info("\nActivated Module IDs: " . implode(', ', $activatedModuleIds));
        $this->info("Pending Module IDs: " . implode(', ', $pendingModuleIds));

        // Ambil semua modul dari database
        $allModules = Module::where('is_active', true)->get();

        $this->info("\nAll active modules in system: " . $allModules->count());
        foreach ($allModules as $module) {
            $this->line("- {$module->name} (ID: {$module->id})");
        }

        // Filter untuk mendapatkan modul yang tersedia tetapi belum aktif/pending
        $availableModules = $allModules->filter(function ($module) use ($activatedModuleIds, $pendingModuleIds) {
            // Cek apakah modul sudah aktif
            $isActive = in_array($module->id, $activatedModuleIds);

            // Cek apakah modul sedang pending
            $isPending = in_array($module->id, $pendingModuleIds);

            // Modul tersedia jika tidak aktif dan tidak pending
            return !$isActive && !$isPending;
        });

        $this->info("\nAvailable modules (not active and not pending): " . $availableModules->count());
        foreach ($availableModules as $module) {
            $this->line("- {$module->name} (ID: {$module->id})");
        }

        $this->info("\nChecking why modules aren't filtering correctly...");
        foreach ($allModules as $module) {
            $isActive = in_array($module->id, $activatedModuleIds);
            $isPending = in_array($module->id, $pendingModuleIds);
            $isAvailable = !$isActive && !$isPending;

            $this->line("Module {$module->name} (ID: {$module->id}):");
            $this->line("  - Is Activated: " . ($isActive ? "Yes" : "No"));
            $this->line("  - Is Pending: " . ($isPending ? "Yes" : "No"));
            $this->line("  - Should be Available: " . ($isAvailable ? "Yes" : "No"));
        }

        return 0;
    }
}
