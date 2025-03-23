<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Tenant;
use App\Services\AuditService;
use App\Services\TenantSelectionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ModuleReportController extends Controller
{
    /**
     * Service untuk audit
     * 
     * @var AuditService
     */
    protected $auditService;

    /**
     * Service untuk pemilihan tenant
     * 
     * @var TenantSelectionService
     */
    protected $tenantSelectionService;

    /**
     * Konstruktor
     * 
     * @param AuditService $auditService
     * @param TenantSelectionService $tenantSelectionService
     */
    public function __construct(
        AuditService $auditService,
        TenantSelectionService $tenantSelectionService
    ) {
        $this->auditService = $auditService;
        $this->tenantSelectionService = $tenantSelectionService;
    }

    /**
     * Menampilkan halaman laporan modul
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Daftar modul aktif
        $activeModules = $this->auditService->getActiveModulesByTenant($tenant);

        // Statistik aktivasi
        $stats = $this->auditService->getModuleActivationStats($startDate, $endDate);

        // Riwayat aktivasi (terbatas 20)
        $activationHistory = $this->auditService->getModuleActivationHistory($tenant, $startDate, $endDate, 20);
        $historyData = $this->auditService->transformModuleHistoryLogs($activationHistory);

        return view('reports.modules.index', [
            'tenant' => $tenant,
            'activeModules' => $activeModules,
            'activationHistory' => $historyData,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Menampilkan histori aktivasi modul
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function activationHistory(Request $request)
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $activationHistory = $this->auditService->getModuleActivationHistory($tenant, $startDate, $endDate);
        $historyData = $this->auditService->transformModuleHistoryLogs($activationHistory);
        $stats = $this->auditService->getModuleActivationStats($startDate, $endDate);

        return view('reports.modules.history', [
            'tenant' => $tenant,
            'activationHistory' => $historyData,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Menampilkan daftar modul aktif per tenant
     * 
     * @return \Illuminate\View\View
     */
    public function activeModules()
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();
        $activeModules = $this->auditService->getActiveModulesByTenant($tenant);

        return view('reports.modules.active', [
            'tenant' => $tenant,
            'activeModules' => $activeModules
        ]);
    }

    /**
     * Mengekspor laporan modul aktif dalam format CSV
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportActiveModules()
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();
        $activeModules = $this->auditService->getActiveModulesByTenant($tenant);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="modul_aktif_' . date('YmdHis') . '.csv"',
        ];

        $callback = function () use ($activeModules) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Tenant ID',
                'Nama Tenant',
                'Kode Tenant',
                'Modul ID',
                'Nama Modul',
                'Kode Modul',
                'Deskripsi Modul',
                'Tanggal Aktivasi',
                'Status',
                'Diaktifkan Oleh'
            ]);

            // Data
            foreach ($activeModules as $module) {
                fputcsv($file, [
                    $module->tenant_id,
                    $module->tenant_name,
                    $module->tenant_code,
                    $module->module_id,
                    $module->module_name,
                    $module->module_code,
                    $module->module_description,
                    $module->activated_at,
                    $module->status ? 'Aktif' : 'Nonaktif',
                    $module->approved_by_name
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Mengekspor histori aktivasi modul dalam format CSV
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportActivationHistory(Request $request)
    {
        $tenant = $this->tenantSelectionService->getActiveTenant();
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $activationHistory = $this->auditService->getModuleActivationHistory($tenant, $startDate, $endDate);
        $historyData = $this->auditService->transformModuleHistoryLogs($activationHistory);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="histori_aktivasi_modul_' . date('YmdHis') . '.csv"',
        ];

        $callback = function () use ($historyData) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
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

            // Data
            foreach ($historyData as $record) {
                fputcsv($file, [
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

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
