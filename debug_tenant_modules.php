<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Mengambil semua modul
$allModules = DB::table('modules')
    ->select('id', 'name', 'code', 'description', 'is_active')
    ->where('is_active', 1)
    ->get();

echo "Semua modul aktif:\n";
foreach ($allModules as $module) {
    echo "ID: {$module->id}, Nama: {$module->name}, Kode: {$module->code}, Aktif: {$module->is_active}\n";
}

// Ambil tenant pertama untuk testing
$tenant = DB::table('tenants')->first();
if (!$tenant) {
    echo "Tidak ada tenant yang ditemukan.\n";
    exit;
}

echo "\nTenant yang digunakan: ID: {$tenant->id}, Nama: {$tenant->name}\n";

// Ambil modul yang sudah diaktifkan untuk tenant ini
$activatedModules = DB::table('tenant_modules')
    ->where('tenant_id', $tenant->id)
    ->where('tenant_modules.is_active', 1)
    ->join('modules', 'tenant_modules.module_id', '=', 'modules.id')
    ->select('modules.id', 'modules.name', 'modules.code')
    ->get();

echo "\nModul yang sudah diaktifkan untuk tenant ini:\n";
if (count($activatedModules) > 0) {
    foreach ($activatedModules as $module) {
        echo "ID: {$module->id}, Nama: {$module->name}, Kode: {$module->code}\n";
    }
} else {
    echo "Tidak ada modul yang diaktifkan.\n";
}

// Ambil modul yang permintaan aktivasinya pending
$pendingModules = DB::table('module_activation_requests')
    ->where('tenant_id', $tenant->id)
    ->where('status', 'pending')
    ->join('modules', 'module_activation_requests.module_id', '=', 'modules.id')
    ->select('modules.id', 'modules.name', 'modules.code')
    ->get();

echo "\nModul dengan permintaan aktivasi pending:\n";
if (count($pendingModules) > 0) {
    foreach ($pendingModules as $module) {
        echo "ID: {$module->id}, Nama: {$module->name}, Kode: {$module->code}\n";
    }
} else {
    echo "Tidak ada permintaan aktivasi yang pending.\n";
}

// Hitung modul yang seharusnya tersedia (belum aktif dan tidak pending)
$availableModuleIds = $allModules->pluck('id')->toArray();
$activatedModuleIds = $activatedModules->pluck('id')->toArray();
$pendingModuleIds = $pendingModules->pluck('id')->toArray();

$availableModules = $allModules->filter(function ($module) use ($activatedModuleIds, $pendingModuleIds) {
    return !in_array($module->id, $activatedModuleIds) && !in_array($module->id, $pendingModuleIds);
});

echo "\nModul yang seharusnya tersedia untuk diaktifkan:\n";
if (count($availableModules) > 0) {
    foreach ($availableModules as $module) {
        echo "ID: {$module->id}, Nama: {$module->name}, Kode: {$module->code}\n";
    }
} else {
    echo "Tidak ada modul yang tersedia untuk diaktifkan.\n";
}

echo "\nAnalisis filter:\n";
foreach ($allModules as $module) {
    $isActivated = in_array($module->id, $activatedModuleIds);
    $isPending = in_array($module->id, $pendingModuleIds);
    $isAvailable = !$isActivated && !$isPending;

    echo "Modul {$module->name} (ID: {$module->id}):\n";
    echo "  - Sudah diaktifkan: " . ($isActivated ? "Ya" : "Tidak") . "\n";
    echo "  - Permintaan pending: " . ($isPending ? "Ya" : "Tidak") . "\n";
    echo "  - Tersedia untuk diaktifkan: " . ($isAvailable ? "Ya" : "Tidak") . "\n";
}
