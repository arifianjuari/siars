<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncidentType;
use App\Models\IncidentSubtype;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Tampilkan halaman pengaturan jenis insiden
     */
    public function incidentTypes()
    {
        $incidentTypes = IncidentType::with('subtypes')->get();
        return view('risk-management.settings.incident-types', compact('incidentTypes'));
    }

    /**
     * Simpan jenis insiden baru
     */
    public function storeIncidentType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        IncidentType::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Jenis insiden berhasil ditambahkan');
    }

    /**
     * Update jenis insiden
     */
    public function updateIncidentType(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $incidentType = IncidentType::findOrFail($id);
        $incidentType->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Jenis insiden berhasil diperbarui');
    }

    /**
     * Hapus jenis insiden
     */
    public function deleteIncidentType($id)
    {
        $incidentType = IncidentType::findOrFail($id);

        // Cek apakah jenis insiden ini memiliki insiden terkait
        if ($incidentType->incidents()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus jenis insiden yang memiliki insiden terkait');
        }

        $incidentType->delete();
        return redirect()->back()->with('success', 'Jenis insiden berhasil dihapus');
    }

    /**
     * Simpan subtipe insiden baru
     */
    public function storeIncidentSubtype(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'incident_type_id' => 'required|exists:incident_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        IncidentSubtype::create([
            'incident_type_id' => $request->incident_type_id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Subtipe insiden berhasil ditambahkan');
    }

    /**
     * Update subtipe insiden
     */
    public function updateIncidentSubtype(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $incidentSubtype = IncidentSubtype::findOrFail($id);
        $incidentSubtype->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Subtipe insiden berhasil diperbarui');
    }

    /**
     * Hapus subtipe insiden
     */
    public function deleteIncidentSubtype($id)
    {
        $incidentSubtype = IncidentSubtype::findOrFail($id);

        // Cek apakah subtipe insiden ini memiliki insiden terkait
        if ($incidentSubtype->incidents()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus subtipe insiden yang memiliki insiden terkait');
        }

        $incidentSubtype->delete();
        return redirect()->back()->with('success', 'Subtipe insiden berhasil dihapus');
    }

    /**
     * Tampilkan halaman pengaturan lokasi
     */
    public function locations()
    {
        $locations = Location::all();
        return view('risk-management.settings.locations', compact('locations'));
    }

    /**
     * Simpan lokasi baru
     */
    public function storeLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Location::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Lokasi berhasil ditambahkan');
    }

    /**
     * Update lokasi
     */
    public function updateLocation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $location = Location::findOrFail($id);
        $location->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Lokasi berhasil diperbarui');
    }

    /**
     * Hapus lokasi
     */
    public function deleteLocation($id)
    {
        $location = Location::findOrFail($id);

        // Cek apakah lokasi ini memiliki insiden terkait
        if ($location->incidents()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus lokasi yang memiliki insiden terkait');
        }

        $location->delete();
        return redirect()->back()->with('success', 'Lokasi berhasil dihapus');
    }

    /**
     * Tampilkan halaman pengaturan level risiko
     */
    public function riskLevels()
    {
        return view('risk-management.settings.risk-levels');
    }

    /**
     * Tampilkan halaman pengaturan matriks risiko
     */
    public function riskMatrix()
    {
        return view('risk-management.settings.risk-matrix');
    }

    /**
     * Menampilkan halaman settings utama
     */
    public function index()
    {
        return view('risk-management.settings.index');
    }

    /**
     * Menampilkan halaman kategori risiko
     */
    public function categories()
    {
        try {
            // Debug untuk melihat apakah controller dipanggil
            Log::info('SettingsController::categories() dipanggil');

            // Cek tenant_id user terlebih dahulu
            $tenantId = Auth::user()->tenant_id ?? null;
            Log::info('Tenant ID: ' . ($tenantId ?? 'NULL'));

            // Buat query dasar
            $categories = \App\Models\RiskCategory::with('parent');

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
            Log::error('Error di SettingsController::categories(): ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->view('errors.custom', [
                'message' => 'Terjadi kesalahan saat menampilkan kategori risiko: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman faktor penyebab risiko
     */
    public function factors()
    {
        try {
            // Debug untuk melihat apakah controller dipanggil
            Log::info('SettingsController::factors() dipanggil');

            // Cek tenant_id user terlebih dahulu
            $tenantId = Auth::user()->tenant_id ?? null;
            Log::info('Tenant ID: ' . ($tenantId ?? 'NULL'));

            // Buat query dasar
            $factors = \App\Models\RiskFactor::query();

            // Dapatkan semua data tanpa filter tenant_id untuk sementara
            $factors = $factors->get();
            Log::info('Jumlah faktor: ' . $factors->count());

            // Cek apakah view ada
            $viewName = 'risk-management.settings.factors';
            $viewExists = view()->exists($viewName);
            Log::info('View ' . $viewName . ' exists: ' . ($viewExists ? 'Yes' : 'No'));

            if (!$viewExists) {
                abort(500, 'View tidak ditemukan: ' . $viewName);
            }

            return view($viewName, compact('factors'));
        } catch (\Exception $e) {
            Log::error('Error di SettingsController::factors(): ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->view('errors.custom', [
                'message' => 'Terjadi kesalahan saat menampilkan faktor penyebab risiko: ' . $e->getMessage()
            ], 500);
        }
    }
}
