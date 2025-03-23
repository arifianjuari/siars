<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\RootCauseAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MonitoringController extends Controller
{
    /**
     * Display a listing of incidents for monitoring
     */
    public function index()
    {
        $incidents = Incident::with(['classification', 'rootCauseAnalysis', 'type', 'location', 'reporter'])
            ->whereHas('rootCauseAnalysis')
            ->latest('updated_at')
            ->paginate(10);

        return view('risk-management.monitoring.index', compact('incidents'));
    }

    /**
     * Show the form for editing the specified incident monitoring.
     */
    public function edit($id)
    {
        $incident = Incident::with(['classification', 'rootCauseAnalysis', 'type', 'subtype', 'location', 'reporter'])
            ->findOrFail($id);

        // Pastikan bahwa insiden sudah dianalisis
        if (!$incident->rootCauseAnalysis) {
            return redirect()->route('risk-management.analysis.create', ['incident_id' => $incident->id])
                ->with('error', 'Insiden ini belum dianalisis. Silakan lakukan analisis terlebih dahulu.');
        }

        return view('risk-management.monitoring.edit', compact('incident'));
    }

    /**
     * Update the specified incident monitoring in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'handling_actions' => 'required|string',
            'handling_date' => 'required|date',
            'handling_result' => 'required|string',
            'follow_up_plan' => 'nullable|string',
            'status' => 'required|in:Dianalisis,Ditangani,Monitoring,Selesai',
            'handling_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $incident = Incident::findOrFail($id);

        $incident->handling_actions = $request->handling_actions;
        $incident->handling_date = $request->handling_date;
        $incident->handling_result = $request->handling_result;
        $incident->follow_up_plan = $request->follow_up_plan;
        $incident->status = $request->status;
        $incident->handler_id = Auth::id();

        // Jika status selesai, tetapkan tanggal penyelesaian
        if ($request->status === 'Selesai') {
            $incident->completed_at = now();
        }

        // Jika ada lampiran dokumen penanganan, simpan
        if ($request->hasFile('handling_document')) {
            // Hapus file lama jika ada
            if ($incident->handling_document) {
                Storage::delete('public/documents/incidents/' . $incident->handling_document);
            }

            $file = $request->file('handling_document');
            $fileName = 'handling_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/documents/incidents', $fileName);
            $incident->handling_document = $fileName;
        }

        $incident->save();

        return redirect()->route('risk-management.incidents.show', $incident->id)
            ->with('success', 'Status penanganan insiden berhasil diperbarui.');
    }
}
