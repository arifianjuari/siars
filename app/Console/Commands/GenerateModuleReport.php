<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\ModuleActivationLog;
use App\Models\Tenant;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateModuleReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:modules 
                            {--type=both : Jenis laporan: active, history, atau both} 
                            {--tenant= : ID tenant spesifik} 
                            {--format=csv : Format output (csv, json, html)} 
                            {--from= : Tanggal awal (format: Y-m-d)} 
                            {--to= : Tanggal akhir (format: Y-m-d)}
                            {--output= : Path output file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat laporan aktivasi modul (modul aktif per tenant dan histori aktivasi)';

    /**
     * Audit service
     * 
     * @var AuditService
     */
    protected $auditService;

    /**
     * Create a new command instance.
     */
    public function __construct(AuditService $auditService)
    {
        parent::__construct();
        $this->auditService = $auditService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Ambil parameter
            $reportType = $this->option('type');
            $tenantId = $this->option('tenant');
            $format = strtolower($this->option('format'));
            $fromDate = $this->option('from');
            $toDate = $this->option('to');

            // Cek validitas tanggal
            if ($fromDate && !$this->isValidDate($fromDate)) {
                $this->error("Format tanggal awal tidak valid. Gunakan format Y-m-d (contoh: 2024-03-21)");
                return Command::FAILURE;
            }

            if ($toDate && !$this->isValidDate($toDate)) {
                $this->error("Format tanggal akhir tidak valid. Gunakan format Y-m-d (contoh: 2024-03-21)");
                return Command::FAILURE;
            }

            // Set default tanggal
            if (!$fromDate) {
                $fromDate = Carbon::now()->subMonths(1)->format('Y-m-d');
            }

            if (!$toDate) {
                $toDate = Carbon::now()->format('Y-m-d');
            }

            // Ambil tenant jika diberikan
            $tenant = null;
            if ($tenantId) {
                $tenant = Tenant::find($tenantId);
                if (!$tenant) {
                    $this->error("Tenant dengan ID {$tenantId} tidak ditemukan");
                    return Command::FAILURE;
                }
            }

            $this->info("Membuat laporan modul dengan tipe: {$reportType}");
            $this->info("Periode: {$fromDate} s/d {$toDate}");

            switch ($reportType) {
                case 'active':
                    $result = $this->generateActiveModulesReport($tenant);
                    break;

                case 'history':
                    $result = $this->generateModuleHistoryReport($tenant, $fromDate, $toDate);
                    break;

                case 'both':
                default:
                    $activeReport = $this->generateActiveModulesReport($tenant);
                    $historyReport = $this->generateModuleHistoryReport($tenant, $fromDate, $toDate);
                    $result = [
                        'active_modules' => $activeReport,
                        'module_history' => $historyReport,
                    ];
                    break;
            }

            // Buat laporan dalam format yang diminta
            $outputPath = $this->generateOutputFile($result, $format, $reportType);

            $this->info("Laporan berhasil dibuat: {$outputPath}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error saat membuat laporan: {$e->getMessage()}");
            Log::error("Error saat membuat laporan modul: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Membuat laporan modul aktif per tenant
     * 
     * @param Tenant|null $tenant
     * @return Collection|array
     */
    protected function generateActiveModulesReport(?Tenant $tenant = null)
    {
        $this->info("Mengambil data modul aktif" . ($tenant ? " untuk tenant {$tenant->name}" : ""));

        return $this->auditService->getActiveModulesByTenant($tenant);
    }

    /**
     * Membuat laporan histori aktivasi modul
     * 
     * @param Tenant|null $tenant
     * @param string $fromDate
     * @param string $toDate
     * @return Collection|array
     */
    protected function generateModuleHistoryReport(?Tenant $tenant = null, string $fromDate, string $toDate)
    {
        $this->info("Mengambil data histori aktivasi modul" . ($tenant ? " untuk tenant {$tenant->name}" : ""));

        $logs = $this->auditService->getModuleActivationHistory($tenant, $fromDate, $toDate);
        $transformedResult = $this->auditService->transformModuleHistoryLogs($logs);

        $this->info("Ditemukan {$transformedResult->count()} catatan aktivitas modul");

        // Tambahkan statistik
        $stats = $this->auditService->getModuleActivationStats($fromDate, $toDate);

        return [
            'records' => $transformedResult,
            'statistics' => $stats,
        ];
    }

    /**
     * Buat file output berdasarkan format yang diminta
     * 
     * @param mixed $data
     * @param string $format
     * @param string $reportType
     * @return string Path file output
     */
    protected function generateOutputFile($data, string $format, string $reportType): string
    {
        $timestamp = Carbon::now()->format('YmdHis');
        $userOutputPath = $this->option('output');

        if ($userOutputPath) {
            $outputPath = $userOutputPath;
        } else {
            $directory = public_path('reports');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $outputPath = "{$directory}/module_report_{$reportType}_{$timestamp}.{$format}";
        }

        switch ($format) {
            case 'json':
                file_put_contents($outputPath, json_encode($data, JSON_PRETTY_PRINT));
                break;

            case 'html':
                $html = $this->convertToHtml($data, $reportType);
                file_put_contents($outputPath, $html);
                break;

            case 'csv':
            default:
                $this->writeCsvFile($data, $outputPath, $reportType);
                break;
        }

        return $outputPath;
    }

    /**
     * Konversi data ke CSV
     * 
     * @param mixed $data
     * @param string $outputPath
     * @param string $reportType
     */
    protected function writeCsvFile($data, string $outputPath, string $reportType): void
    {
        $fp = fopen($outputPath, 'w');

        if ($reportType === 'active' || $reportType === 'both') {
            $activeModules = $reportType === 'both' ? $data['active_modules'] : $data;

            // Tulis header
            fputcsv($fp, [
                'Tenant ID',
                'Tenant Name',
                'Tenant Code',
                'Module ID',
                'Module Name',
                'Module Code',
                'Module Description',
                'Activated At',
                'Status',
                'Approved By'
            ]);

            // Tulis data
            foreach ($activeModules as $module) {
                fputcsv($fp, [
                    $module->tenant_id,
                    $module->tenant_name,
                    $module->tenant_code,
                    $module->module_id,
                    $module->module_name,
                    $module->module_code,
                    $module->module_description,
                    $module->activated_at,
                    $module->status ? 'Aktif' : 'Nonaktif',
                    $module->approved_by_name ?? '-'
                ]);
            }

            if ($reportType === 'both') {
                // Tambahkan pemisah
                fputcsv($fp, []);
                fputcsv($fp, ['--- HISTORI AKTIVASI MODUL ---']);
                fputcsv($fp, []);
            }
        }

        if ($reportType === 'history' || $reportType === 'both') {
            $history = $reportType === 'both' ? $data['module_history'] : $data;

            // Tulis statistics
            fputcsv($fp, ['STATISTIK AKTIVASI']);
            foreach ($history['statistics'] as $key => $value) {
                fputcsv($fp, [str_replace('_', ' ', ucfirst($key)), $value]);
            }

            // Tambahkan pemisah
            fputcsv($fp, []);
            fputcsv($fp, ['CATATAN AKTIVASI']);

            // Tulis header
            fputcsv($fp, [
                'ID',
                'Aksi',
                'Waktu',
                'Modul ID',
                'Nama Modul',
                'Kode Modul',
                'Tenant ID',
                'Nama Tenant',
                'Kode Tenant',
                'User ID',
                'Nama User',
                'Catatan',
                'Request ID'
            ]);

            // Tulis data
            foreach ($history['records'] as $record) {
                fputcsv($fp, [
                    $record['id'],
                    $record['action_text'],
                    $record['action_at'],
                    $record['module_id'],
                    $record['module_name'],
                    $record['module_code'],
                    $record['tenant_id'],
                    $record['tenant_name'],
                    $record['tenant_code'],
                    $record['user_id'],
                    $record['user_name'],
                    $record['notes'],
                    $record['request_id']
                ]);
            }
        }

        fclose($fp);
    }

    /**
     * Konversi data ke HTML
     * 
     * @param mixed $data
     * @param string $reportType
     * @return string
     */
    protected function convertToHtml($data, string $reportType): string
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Modul SIARS</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1, h2 { color: #2c3e50; }
                table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .stats { display: flex; flex-wrap: wrap; margin-bottom: 20px; }
                .stat-box { background: #f1f8ff; border: 1px solid #cce5ff; border-radius: 5px; padding: 15px; margin: 10px; min-width: 170px; }
                .stat-value { font-size: 24px; font-weight: bold; color: #0066cc; margin: 5px 0; }
                .stat-label { font-size: 14px; color: #666; }
            </style>
        </head>
        <body>
            <h1>Laporan Modul SIARS</h1>
            <p>Laporan dibuat pada: ' . Carbon::now()->format('Y-m-d H:i:s') . '</p>';

        if ($reportType === 'active' || $reportType === 'both') {
            $activeModules = $reportType === 'both' ? $data['active_modules'] : $data;

            $html .= '
            <h2>Modul Aktif Per Tenant</h2>
            <table>
                <tr>
                    <th>Tenant</th>
                    <th>Modul</th>
                    <th>Kode Modul</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Aktivasi</th>
                    <th>Status</th>
                    <th>Diaktifkan Oleh</th>
                </tr>';

            foreach ($activeModules as $module) {
                $html .= "
                <tr>
                    <td>{$module->tenant_name} ({$module->tenant_code})</td>
                    <td>{$module->module_name}</td>
                    <td>{$module->module_code}</td>
                    <td>{$module->module_description}</td>
                    <td>{$module->activated_at}</td>
                    <td>" . ($module->status ? 'Aktif' : 'Nonaktif') . "</td>
                    <td>" . ($module->approved_by_name ?? '-') . "</td>
                </tr>";
            }

            $html .= '</table>';
        }

        if ($reportType === 'history' || $reportType === 'both') {
            $history = $reportType === 'both' ? $data['module_history'] : $data;
            $stats = $history['statistics'];

            $html .= '
            <h2>Statistik Aktivasi Modul</h2>
            <div class="stats">';

            foreach ($stats as $key => $value) {
                $label = str_replace('_', ' ', ucfirst($key));
                $html .= "
                <div class='stat-box'>
                    <div class='stat-value'>{$value}</div>
                    <div class='stat-label'>{$label}</div>
                </div>";
            }

            $html .= '</div>
            <h2>Histori Aktivasi Modul</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Aksi</th>
                    <th>Waktu</th>
                    <th>Modul</th>
                    <th>Tenant</th>
                    <th>User</th>
                    <th>Catatan</th>
                </tr>';

            foreach ($history['records'] as $record) {
                $html .= "
                <tr>
                    <td>{$record['id']}</td>
                    <td>{$record['action_text']}</td>
                    <td>{$record['action_at']}</td>
                    <td>{$record['module_name']} ({$record['module_code']})</td>
                    <td>{$record['tenant_name']} ({$record['tenant_code']})</td>
                    <td>{$record['user_name']}</td>
                    <td>{$record['notes']}</td>
                </tr>";
            }

            $html .= '</table>';
        }

        $html .= '
        </body>
        </html>';

        return $html;
    }

    /**
     * Validasi format tanggal
     * 
     * @param string $date
     * @return bool
     */
    protected function isValidDate(string $date): bool
    {
        $format = 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
