<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiskReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('risk-management.reviews.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('risk-management.reviews.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'category_id' => 'required|exists:risk_categories,id',
            'description' => 'required|string',
            'impact' => 'required|integer|min:1|max:5',
            'probability' => 'required|integer|min:1|max:5',
            'mitigation' => 'required|string',
        ]);

        // Logika penyimpanan akan diimplementasikan sesuai kebutuhan

        return redirect()->route('risk-management.reviews.index')
            ->with('success', 'Penilaian risiko berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('risk-management.reviews.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('risk-management.reviews.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $validated = $request->validate([
            'category_id' => 'required|exists:risk_categories,id',
            'description' => 'required|string',
            'impact' => 'required|integer|min:1|max:5',
            'probability' => 'required|integer|min:1|max:5',
            'mitigation' => 'required|string',
        ]);

        // Logika update akan diimplementasikan sesuai kebutuhan

        return redirect()->route('risk-management.reviews.index')
            ->with('success', 'Penilaian risiko berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Method ini tidak diimplementasikan untuk mencegah penghapusan data
        return redirect()->route('risk-management.reviews.index')
            ->with('info', 'Penghapusan penilaian risiko tidak diperbolehkan');
    }
}
