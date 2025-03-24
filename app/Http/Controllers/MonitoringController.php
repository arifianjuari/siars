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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Redirect to incidents list as we don't create monitors directly
        return redirect()->route('risk-management.incidents.index')
            ->with('info', 'Monitoring hanya dapat dilakukan pada insiden yang sudah dianalisis.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not used - we don't create new monitoring records
        return redirect()->route('risk-management.incidents.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $incident = Incident::with(['classification', 'rootCauseAnalysis', 'type', 'subtype', 'location', 'reporter'])
            ->findOrFail($id);

        // Pastikan bahwa insiden sudah dianalisis
        if (!$incident->rootCauseAnalysis) {
            return redirect()->route('risk-management.analysis.create', ['incident_id' => $incident->id])
                ->with('error', 'Insiden ini belum dianalisis. Silakan lakukan analisis terlebih dahulu.');
        }

        // Redirect to edit view which handles both viewing and editing
        return $this->edit($id);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Not used - we don't delete monitoring records
        return redirect()->route('risk-management.monitoring.index')
            ->with('error', 'Penghapusan monitoring tidak diizinkan.');
    }
}
