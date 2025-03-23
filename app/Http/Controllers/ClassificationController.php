<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\RiskClassification;
use Illuminate\Support\Facades\Auth;

class ClassificationController extends Controller
{
    /**
     * Tampilkan daftar klasifikasi risiko.
     */
    public function index()
    {
        $classifications = RiskClassification::with('incident')->get();
        return view('risk-management.classifications.index', compact('classifications'));
    }

    /**
     * Tampilkan form untuk membuat klasifikasi risiko baru.
     */
    public function create(Request $request)
    {
        $incident_id = $request->input('incident_id');
        $incident = Incident::findOrFail($incident_id);

        // Cek apakah insiden sudah diklasifikasi
        if ($incident->classification) {
            return redirect()->route('risk-management.incidents.show', $incident->id)
                ->with('warning', 'Insiden ini sudah diklasifikasi sebelumnya.');
        }

        return view('risk-management.classifications.create', compact('incident'));
    }

    /**
     * Simpan klasifikasi risiko baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_id' => 'required|exists:incidents,id',
            'dampak' => 'required|integer|min:1|max:5',
            'dampak_detail' => 'required|string',
            'probabilitas' => 'required|integer|min:1|max:5',
            'probabilitas_detail' => 'required|string',
        ]);

        // Hitung skor risiko
        $skor_risiko = $validated['dampak'] * $validated['probabilitas'];

        // Tentukan level dan zona risiko
        $level_risiko = $this->determineRiskLevel($skor_risiko);
        $zona_risiko = $this->determineRiskZone($skor_risiko);

        // Buat klasifikasi risiko
        $classification = RiskClassification::create([
            'incident_id' => $validated['incident_id'],
            'dampak' => $validated['dampak'],
            'dampak_detail' => $validated['dampak_detail'],
            'probabilitas' => $validated['probabilitas'],
            'probabilitas_detail' => $validated['probabilitas_detail'],
            'skor_risiko' => $skor_risiko,
            'level_risiko' => $level_risiko,
            'zona_risiko' => $zona_risiko,
            'classifier_id' => Auth::id(),
        ]);

        // Update status insiden
        $incident = Incident::find($validated['incident_id']);
        $incident->update(['status' => 'Proses']);

        return redirect()->route('risk-management.incidents.show', $validated['incident_id'])
            ->with('success', 'Klasifikasi risiko berhasil disimpan.');
    }

    /**
     * Tampilkan detail klasifikasi risiko.
     */
    public function show($id)
    {
        $classification = RiskClassification::with('incident')->findOrFail($id);
        return view('risk-management.classifications.show', compact('classification'));
    }

    /**
     * Tampilkan form untuk mengedit klasifikasi risiko.
     */
    public function edit($id)
    {
        $classification = RiskClassification::with('incident')->findOrFail($id);
        return view('risk-management.classifications.edit', compact('classification'));
    }

    /**
     * Update klasifikasi risiko di database.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'dampak' => 'required|integer|min:1|max:5',
            'dampak_detail' => 'required|string',
            'probabilitas' => 'required|integer|min:1|max:5',
            'probabilitas_detail' => 'required|string',
        ]);

        $classification = RiskClassification::findOrFail($id);

        // Hitung skor risiko
        $skor_risiko = $validated['dampak'] * $validated['probabilitas'];

        // Tentukan level dan zona risiko
        $level_risiko = $this->determineRiskLevel($skor_risiko);
        $zona_risiko = $this->determineRiskZone($skor_risiko);

        // Update klasifikasi
        $classification->update([
            'dampak' => $validated['dampak'],
            'dampak_detail' => $validated['dampak_detail'],
            'probabilitas' => $validated['probabilitas'],
            'probabilitas_detail' => $validated['probabilitas_detail'],
            'skor_risiko' => $skor_risiko,
            'level_risiko' => $level_risiko,
            'zona_risiko' => $zona_risiko,
        ]);

        return redirect()->route('risk-management.classifications.show', $id)
            ->with('success', 'Klasifikasi risiko berhasil diperbarui.');
    }

    /**
     * Hapus klasifikasi risiko dari database.
     */
    public function destroy($id)
    {
        $classification = RiskClassification::findOrFail($id);
        $incident_id = $classification->incident_id;

        $classification->delete();

        return redirect()->route('risk-management.incidents.show', $incident_id)
            ->with('success', 'Klasifikasi risiko berhasil dihapus.');
    }

    /**
     * Tentukan level risiko berdasarkan skor.
     */
    private function determineRiskLevel($score)
    {
        if ($score >= 1 && $score <= 3) {
            return 'Rendah';
        } elseif ($score >= 4 && $score <= 9) {
            return 'Sedang';
        } elseif ($score >= 10 && $score <= 15) {
            return 'Tinggi';
        } else {
            return 'Ekstrim';
        }
    }

    /**
     * Tentukan zona risiko berdasarkan skor.
     */
    private function determineRiskZone($score)
    {
        if ($score >= 1 && $score <= 3) {
            return 'Hijau';
        } elseif ($score >= 4 && $score <= 9) {
            return 'Kuning';
        } else {
            return 'Merah';
        }
    }
}
