<?php

namespace App\Http\Controllers;

use App\Models\RiskFactor;
use App\Models\RiskReport;
use Illuminate\Http\Request;

class RiskFactorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('risk-management.factors.index', [
            'factors' => RiskFactor::with('report')->latest()->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $reports = RiskReport::orderBy('report_number')->get();
        return view('risk-management.factors.create', [
            'reports' => $reports
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_id' => 'required|exists:risk_reports,id',
            'factor_type' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        RiskFactor::create($validated);

        return redirect()->route('risk-management.factors.index')
            ->with('success', 'Faktor penyebab risiko berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RiskFactor $factor)
    {
        return view('risk-management.factors.show', [
            'factor' => $factor->load('report')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiskFactor $factor)
    {
        $reports = RiskReport::orderBy('report_number')->get();
        return view('risk-management.factors.edit', [
            'factor' => $factor,
            'reports' => $reports
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiskFactor $factor)
    {
        $validated = $request->validate([
            'report_id' => 'required|exists:risk_reports,id',
            'factor_type' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $factor->update($validated);

        return redirect()->route('risk-management.factors.index')
            ->with('success', 'Faktor penyebab risiko berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiskFactor $factor)
    {
        $factor->delete();

        return redirect()->route('risk-management.factors.index')
            ->with('success', 'Faktor penyebab risiko berhasil dihapus.');
    }
}
