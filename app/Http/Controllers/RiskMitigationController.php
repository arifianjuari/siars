<?php

namespace App\Http\Controllers;

use App\Models\RiskMitigation;
use App\Models\RiskReport;
use App\Models\User;
use Illuminate\Http\Request;

class RiskMitigationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('risk-management.mitigations.index', [
            'mitigations' => RiskMitigation::with(['report', 'responsible'])->latest()->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $reports = RiskReport::orderBy('report_number')->get();
        $responsibles = User::orderBy('name')->get();

        return view('risk-management.mitigations.create', [
            'reports' => $reports,
            'responsibles' => $responsibles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_id' => 'required|exists:risk_reports,id',
            'action_description' => 'required|string',
            'responsible_id' => 'required|exists:users,id',
            'target_date' => 'required|date',
            'status' => 'required|in:planned,in_progress,completed',
            'completion_date' => 'nullable|date|required_if:status,completed',
            'effectiveness' => 'nullable|in:effective,partially,not_effective|required_if:status,completed',
        ]);

        RiskMitigation::create($validated);

        return redirect()->route('risk-management.mitigations.index')
            ->with('success', 'Mitigasi risiko berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RiskMitigation $mitigation)
    {
        return view('risk-management.mitigations.show', [
            'mitigation' => $mitigation->load(['report', 'responsible'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiskMitigation $mitigation)
    {
        $reports = RiskReport::orderBy('report_number')->get();
        $responsibles = User::orderBy('name')->get();

        return view('risk-management.mitigations.edit', [
            'mitigation' => $mitigation,
            'reports' => $reports,
            'responsibles' => $responsibles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiskMitigation $mitigation)
    {
        $validated = $request->validate([
            'report_id' => 'required|exists:risk_reports,id',
            'action_description' => 'required|string',
            'responsible_id' => 'required|exists:users,id',
            'target_date' => 'required|date',
            'status' => 'required|in:planned,in_progress,completed',
            'completion_date' => 'nullable|date|required_if:status,completed',
            'effectiveness' => 'nullable|in:effective,partially,not_effective|required_if:status,completed',
        ]);

        $mitigation->update($validated);

        return redirect()->route('risk-management.mitigations.index')
            ->with('success', 'Mitigasi risiko berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiskMitigation $mitigation)
    {
        $mitigation->delete();

        return redirect()->route('risk-management.mitigations.index')
            ->with('success', 'Mitigasi risiko berhasil dihapus.');
    }
}
