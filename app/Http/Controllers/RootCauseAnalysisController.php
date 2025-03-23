<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\RootCauseAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RootCauseAnalysisController extends Controller
{
    /**
     * Display a listing of the analyses.
     */
    public function index()
    {
        $analyses = RootCauseAnalysis::with(['incident'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('risk-management.analysis.index', compact('analyses'));
    }

    /**
     * Show the form for creating a new analysis.
     */
    public function create(Request $request)
    {
        $incidentId = $request->input('incident_id');
        $incident = Incident::with('classification')->findOrFail($incidentId);

        // Pastikan bahwa kejadian memiliki klasifikasi risiko dulu
        if (!$incident->classification) {
            return redirect()->route('risk-management.classifications.create', ['incident_id' => $incident->id])
                ->with('error', 'Silakan klasifikasikan risiko insiden terlebih dahulu.');
        }

        // Cek apakah sudah ada analisis untuk kejadian ini
        $existingAnalysis = RootCauseAnalysis::where('incident_id', $incidentId)->first();
        if ($existingAnalysis) {
            return redirect()->route('risk-management.analysis.edit', ['analysis' => $existingAnalysis->id])
                ->with('info', 'Analisis akar masalah sudah ada untuk insiden ini.');
        }

        return view('risk-management.analysis.create', compact('incident'));
    }

    /**
     * Store a newly created analysis in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'incident_id' => 'required|exists:incidents,id',
            'team_factors' => 'nullable|string',
            'system_factors' => 'nullable|string',
            'patient_factors' => 'nullable|string',
            'environmental_factors' => 'nullable|string',
            'analysis_method' => 'required|string',
            'root_causes' => 'required|string',
            'recommendations' => 'required|string',
            'faktor_tim' => 'nullable|array',
            'faktor_sistem' => 'nullable|array',
            'faktor_pasien' => 'nullable|array',
            'faktor_lingkungan' => 'nullable|array',
        ]);

        // Proses faktor tim dari checkbox
        $teamFactors = $request->team_factors ?? '';
        if ($request->has('faktor_tim') && is_array($request->faktor_tim)) {
            $teamFactors = implode(", ", $request->faktor_tim) . ($teamFactors ? "\n\nLainnya: " . $teamFactors : '');
        }

        // Proses faktor sistem dari checkbox
        $systemFactors = $request->system_factors ?? '';
        if ($request->has('faktor_sistem') && is_array($request->faktor_sistem)) {
            $systemFactors = implode(", ", $request->faktor_sistem) . ($systemFactors ? "\n\nLainnya: " . $systemFactors : '');
        }

        // Proses faktor pasien dari checkbox
        $patientFactors = $request->patient_factors ?? '';
        if ($request->has('faktor_pasien') && is_array($request->faktor_pasien)) {
            $patientFactors = implode(", ", $request->faktor_pasien) . ($patientFactors ? "\n\nLainnya: " . $patientFactors : '');
        }

        // Proses faktor lingkungan dari checkbox
        $environmentalFactors = $request->environmental_factors ?? '';
        if ($request->has('faktor_lingkungan') && is_array($request->faktor_lingkungan)) {
            $environmentalFactors = implode(", ", $request->faktor_lingkungan) . ($environmentalFactors ? "\n\nLainnya: " . $environmentalFactors : '');
        }

        $analysis = new RootCauseAnalysis();
        $analysis->incident_id = $request->incident_id;
        $analysis->team_factors = $teamFactors;
        $analysis->system_factors = $systemFactors;
        $analysis->patient_factors = $patientFactors;
        $analysis->environmental_factors = $environmentalFactors;
        $analysis->analysis_method = $request->analysis_method;
        $analysis->root_causes = $request->root_causes;
        $analysis->recommendations = $request->recommendations;
        $analysis->analyzed_by = Auth::id();
        $analysis->analyzed_at = now();

        // Tambahkan nilai untuk kolom-kolom lama
        $analysis->penyebab_langsung = $request->root_causes; // Isi dengan nilai yang sama dengan root_causes
        $analysis->teknik_analisis = $request->analysis_method; // Isi dengan nilai yang sama dengan analysis_method
        $analysis->rekomendasi_pendek = $request->recommendations; // Isi dengan nilai yang sama dengan recommendations
        $analysis->analyzer_id = Auth::id(); // Isi dengan nilai yang sama dengan analyzed_by

        $analysis->save();

        // Update status insiden menjadi "Proses"
        $incident = Incident::find($request->incident_id);
        $incident->status = 'Proses';
        $incident->save();

        return redirect()->route('risk-management.incidents.show', $request->incident_id)
            ->with('success', 'Analisis akar masalah berhasil disimpan.');
    }

    /**
     * Display the specified analysis.
     */
    public function show(RootCauseAnalysis $analysis)
    {
        $analysis->load('incident');
        return view('risk-management.analysis.show', compact('analysis'));
    }

    /**
     * Show the form for editing the specified analysis.
     */
    public function edit(RootCauseAnalysis $analysis)
    {
        $analysis->load('incident.classification');
        return view('risk-management.analysis.edit', compact('analysis'));
    }

    /**
     * Update the specified analysis in storage.
     */
    public function update(Request $request, RootCauseAnalysis $analysis)
    {
        $request->validate([
            'team_factors' => 'nullable|string',
            'system_factors' => 'nullable|string',
            'patient_factors' => 'nullable|string',
            'environmental_factors' => 'nullable|string',
            'analysis_method' => 'required|string',
            'root_causes' => 'required|string',
            'recommendations' => 'required|string',
            'faktor_tim' => 'nullable|array',
            'faktor_sistem' => 'nullable|array',
            'faktor_pasien' => 'nullable|array',
            'faktor_lingkungan' => 'nullable|array',
        ]);

        // Proses faktor tim dari checkbox
        $teamFactors = $request->team_factors ?? '';
        if ($request->has('faktor_tim') && is_array($request->faktor_tim)) {
            $teamFactors = implode(", ", $request->faktor_tim) . ($teamFactors ? "\n\nLainnya: " . $teamFactors : '');
        }

        // Proses faktor sistem dari checkbox
        $systemFactors = $request->system_factors ?? '';
        if ($request->has('faktor_sistem') && is_array($request->faktor_sistem)) {
            $systemFactors = implode(", ", $request->faktor_sistem) . ($systemFactors ? "\n\nLainnya: " . $systemFactors : '');
        }

        // Proses faktor pasien dari checkbox
        $patientFactors = $request->patient_factors ?? '';
        if ($request->has('faktor_pasien') && is_array($request->faktor_pasien)) {
            $patientFactors = implode(", ", $request->faktor_pasien) . ($patientFactors ? "\n\nLainnya: " . $patientFactors : '');
        }

        // Proses faktor lingkungan dari checkbox
        $environmentalFactors = $request->environmental_factors ?? '';
        if ($request->has('faktor_lingkungan') && is_array($request->faktor_lingkungan)) {
            $environmentalFactors = implode(", ", $request->faktor_lingkungan) . ($environmentalFactors ? "\n\nLainnya: " . $environmentalFactors : '');
        }

        $analysis->team_factors = $teamFactors;
        $analysis->system_factors = $systemFactors;
        $analysis->patient_factors = $patientFactors;
        $analysis->environmental_factors = $environmentalFactors;
        $analysis->analysis_method = $request->analysis_method;
        $analysis->root_causes = $request->root_causes;
        $analysis->recommendations = $request->recommendations;
        $analysis->updated_by = Auth::id();

        // Tambahkan nilai untuk kolom-kolom lama
        $analysis->penyebab_langsung = $request->root_causes; // Isi dengan nilai yang sama dengan root_causes
        $analysis->teknik_analisis = $request->analysis_method; // Isi dengan nilai yang sama dengan analysis_method
        $analysis->rekomendasi_pendek = $request->recommendations; // Isi dengan nilai yang sama dengan recommendations

        $analysis->save();

        return redirect()->route('risk-management.incidents.show', $analysis->incident_id)
            ->with('success', 'Analisis akar masalah berhasil diperbarui.');
    }
}
