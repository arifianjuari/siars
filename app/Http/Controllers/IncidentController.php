<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\IncidentSubtype;
use App\Models\Location;
use App\Models\User;
use App\Models\Profession;
use App\Models\RiskClassification;
use App\Models\RootCauseAnalysis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    /**
     * Display a listing of the incidents.
     */
    public function index(Request $request)
    {
        $query = Incident::with(['location', 'incidentType', 'incidentSubtype', 'reporter'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_waktu_kejadian', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan lokasi
        if ($request->has('location_id') && $request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        // Filter berdasarkan jenis insiden
        if ($request->has('incident_type_id') && $request->incident_type_id) {
            $query->where('incident_type_id', $request->incident_type_id);
        }

        // Filter berdasarkan level risiko
        if ($request->has('risk_level') && $request->risk_level) {
            $query->whereHas('classification', function ($q) use ($request) {
                $q->where('level_risiko', $request->risk_level);
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan keyword
        if ($request->has('keyword') && $request->keyword) {
            $keyword = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('no_rm', 'like', $keyword)
                    ->orWhere('nama_pasien', 'like', $keyword)
                    ->orWhere('kronologis', 'like', $keyword);
            });
        }

        $incidents = $query->paginate(10);
        $locations = Location::where('is_active', true)->get();
        $incidentTypes = IncidentType::where('is_active', true)->get();

        return view('risk-management.incidents.index', compact('incidents', 'locations', 'incidentTypes'));
    }

    /**
     * Show the form for creating a new incident.
     */
    public function create()
    {
        $locations = Location::where('is_active', true)->get();
        $incidentTypes = IncidentType::where('is_active', true)->get();
        $professions = DB::table('professions')->where('is_active', true)->get();

        return view('risk-management.incidents.create', compact('locations', 'incidentTypes', 'professions'));
    }

    /**
     * Store a newly created incident in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_waktu_kejadian' => 'required|date',
            'lokasi_id' => 'required|exists:locations,id',
            'jenis_insiden_id' => 'required|exists:incident_types,id',
            'incident_subtype_id' => 'nullable|exists:incident_subtypes,id',
            'nama_pasien' => 'required|string|max:255',
            'no_rekam_medis' => 'required|string|max:50',
            'kronologis' => 'required|string',
            'tindakan_langsung' => 'required|string',
            'nama_pelapor' => 'required|string|max:255',
            'profesi_id' => 'required|exists:professions,id',
            'pernah_terjadi_sebelumnya' => 'required|boolean',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Menyesuaikan nama field ke kolom database
        $data = [
            'tanggal_waktu_kejadian' => $validated['tanggal_waktu_kejadian'],
            'location_id' => $validated['lokasi_id'],
            'incident_type_id' => $validated['jenis_insiden_id'],
            'incident_subtype_id' => $validated['incident_subtype_id'],
            'nama_pasien' => $validated['nama_pasien'],
            'no_rm' => $validated['no_rekam_medis'],
            'kronologis' => $validated['kronologis'],
            'tindakan_langsung' => $validated['tindakan_langsung'],
            'nama_pelapor' => $validated['nama_pelapor'],
            'profesi_id' => $validated['profesi_id'],
            'pernah_terjadi_sebelumnya' => $validated['pernah_terjadi_sebelumnya'],
            'reporter_id' => Auth::id(),
            'status' => 'Baru'
        ];

        // Upload dokumen pendukung jika ada
        if ($request->hasFile('dokumen_pendukung')) {
            $path = $request->file('dokumen_pendukung')->store('public/documents/incidents');
            $data['dokumen_pendukung'] = $path;
        }

        $incident = Incident::create($data);

        return redirect()->route('risk-management.incidents.show', $incident)
            ->with('success', 'Insiden berhasil dilaporkan!');
    }

    /**
     * Display the specified incident.
     */
    public function show(Incident $incident)
    {
        $incident->load(['location', 'incidentType', 'incidentSubtype', 'reporter', 'classification', 'analysis']);

        return view('risk-management.incidents.show', compact('incident'));
    }

    /**
     * Show the form for editing the specified incident.
     */
    public function edit(Incident $incident)
    {
        $locations = Location::where('is_active', true)->get();
        $incidentTypes = IncidentType::where('is_active', true)->get();
        $incidentSubtypes = IncidentSubtype::where('incident_type_id', $incident->incident_type_id)
            ->where('is_active', true)
            ->get();

        return view('risk-management.incidents.edit', compact('incident', 'locations', 'incidentTypes', 'incidentSubtypes'));
    }

    /**
     * Update the specified incident in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'tanggal_waktu_kejadian' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'incident_type_id' => 'required|exists:incident_types,id',
            'incident_subtype_id' => 'nullable|exists:incident_subtypes,id',
            'nama_pasien' => 'required|string|max:255',
            'no_rm' => 'required|string|max:50',
            'kronologis' => 'required|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Upload dokumen pendukung jika ada
        if ($request->hasFile('dokumen_pendukung')) {
            // Hapus file lama jika ada
            if ($incident->dokumen_pendukung) {
                Storage::delete($incident->dokumen_pendukung);
            }

            $path = $request->file('dokumen_pendukung')->store('public/documents/incidents');
            $validated['dokumen_pendukung'] = $path;
        }

        $incident->update($validated);

        return redirect()->route('risk-management.incidents.show', $incident)
            ->with('success', 'Insiden berhasil diperbarui!');
    }

    /**
     * Remove the specified incident from storage.
     */
    public function destroy(Incident $incident)
    {
        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Hapus analisis terkait jika ada
            if ($incident->analysis) {
                $incident->analysis->delete();
            }

            // Hapus klasifikasi risiko terkait jika ada
            if ($incident->classification) {
                $incident->classification->delete();
            }

            // Hapus file terkait
            if ($incident->dokumen_pendukung) {
                Storage::delete($incident->dokumen_pendukung);
            }

            if ($incident->dokumen_evaluasi) {
                Storage::delete($incident->dokumen_evaluasi);
            }

            if ($incident->handling_document) {
                Storage::delete('public/documents/incidents/' . $incident->handling_document);
            }

            // Hapus insiden
            $incident->delete();

            // Commit transaksi
            DB::commit();

            return redirect()->route('risk-management.incidents.index')
                ->with('success', 'Insiden berhasil dihapus!');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            // Log error
            Log::error('Error saat menghapus insiden: ' . $e->getMessage());

            return redirect()->route('risk-management.incidents.show', $incident)
                ->with('error', 'Terjadi kesalahan saat menghapus insiden. Silakan coba lagi.');
        }
    }

    /**
     * Ambil subtypes berdasarkan incident type
     */
    public function getSubtypes(Request $request)
    {
        // Debug request
        info('Request getSubtypes: ' . json_encode([
            'incident_type_id' => $request->incident_type_id,
            'all_params' => $request->all(),
            'headers' => $request->header()
        ]));

        if (!$request->has('incident_type_id') || !$request->incident_type_id) {
            return response()->json(['error' => 'ID jenis insiden tidak valid'], 400);
        }

        $subtypes = IncidentSubtype::where('incident_type_id', $request->incident_type_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'is_active']);

        // Debug response
        info('Subtypes found: ' . $subtypes->count() . ', data: ' . json_encode($subtypes));

        return response()->json($subtypes)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
    }

    /**
     * Tampilkan dashboard
     */
    public function dashboard()
    {
        // Statistik insiden
        $totalIncidents = Incident::count();
        $newIncidents = Incident::where('status', 'Baru')->count();
        $inProgressIncidents = Incident::whereIn('status', ['Proses', 'Evaluasi'])->count();
        $completedIncidents = Incident::where('status', 'Selesai')->count();

        // Statistik berdasarkan level risiko
        $lowRisks = RiskClassification::where('level_risiko', 'Rendah')->count();
        $mediumRisks = RiskClassification::where('level_risiko', 'Sedang')->count();
        $highRisks = RiskClassification::where('level_risiko', 'Tinggi')->count();
        $extremeRisks = RiskClassification::where('level_risiko', 'Ekstrim')->count();
        $classifiedCount = $lowRisks + $mediumRisks + $highRisks + $extremeRisks;
        $classifiedPercentage = $totalIncidents > 0 ? round(($classifiedCount / $totalIncidents) * 100) : 0;

        // Insiden terbaru
        $recentIncidents = Incident::with(['location', 'incidentType', 'classification'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Insiden berisiko tinggi
        $highRiskIncidents = Incident::with(['location', 'incidentType', 'classification'])
            ->whereHas('classification', function ($query) {
                $query->whereIn('level_risiko', ['Tinggi', 'Ekstrim']);
            })
            ->where('status', '!=', 'Selesai')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Data untuk grafik bulanan (12 bulan terakhir)
        $monthlyData = Incident::selectRaw('MONTH(tanggal_waktu_kejadian) as month, YEAR(tanggal_waktu_kejadian) as year, COUNT(*) as total')
            ->whereRaw('tanggal_waktu_kejadian >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Data untuk grafik jenis insiden
        $incidentTypeData = Incident::selectRaw('incident_types.name, COUNT(*) as total')
            ->join('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->groupBy('incident_types.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Data untuk grafik lokasi
        $locationData = Incident::selectRaw('locations.name, COUNT(*) as total')
            ->join('locations', 'incidents.location_id', '=', 'locations.id')
            ->groupBy('locations.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Data untuk grafik status penanganan
        $statusData = Incident::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        return view('risk-management.dashboard', compact(
            'totalIncidents',
            'newIncidents',
            'inProgressIncidents',
            'completedIncidents',
            'lowRisks',
            'mediumRisks',
            'highRisks',
            'extremeRisks',
            'classifiedPercentage',
            'recentIncidents',
            'highRiskIncidents',
            'monthlyData',
            'incidentTypeData',
            'locationData',
            'statusData'
        ));
    }
}
