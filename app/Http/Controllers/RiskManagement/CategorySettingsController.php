<?php

namespace App\Http\Controllers\RiskManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategorySettingsController extends Controller
{
    /**
     * Menampilkan halaman pengaturan kategori risiko
     */
    public function index()
    {
        try {
            // Debug untuk melihat apakah controller dipanggil
            Log::info('CategorySettingsController::index() dipanggil');

            // Cek tenant_id user terlebih dahulu
            $tenantId = Auth::user()->tenant_id ?? null;
            Log::info('Tenant ID: ' . ($tenantId ?? 'NULL'));

            // Buat query dasar
            $categories = RiskCategory::with('parent');

            // Dapatkan semua data tanpa filter tenant_id untuk sementara
            $categories = $categories->orderBy('name')->get();
            Log::info('Jumlah kategori: ' . $categories->count());

            // Cek apakah view ada
            $viewName = 'risk-management.settings.categories';
            $viewExists = view()->exists($viewName);
            Log::info('View ' . $viewName . ' exists: ' . ($viewExists ? 'Yes' : 'No'));

            if (!$viewExists) {
                abort(500, 'View tidak ditemukan: ' . $viewName);
            }

            return view($viewName, compact('categories'));
        } catch (\Exception $e) {
            Log::error('Error di CategorySettingsController::index(): ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->view('errors.custom', [
                'message' => 'Terjadi kesalahan saat menampilkan kategori risiko: ' . $e->getMessage()
            ], 500);
        }
    }
}
