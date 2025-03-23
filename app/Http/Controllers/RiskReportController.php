<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiskReportController extends Controller
{
    /**
     * Display a listing of available reports.
     */
    public function index()
    {
        return view('risk-management.reports.index');
    }

    /**
     * Display a specific report.
     */
    public function show(string $type)
    {
        return view('risk-management.reports.show', compact('type'));
    }

    /**
     * Generate a PDF report.
     */
    public function generatePdf(Request $request, string $type)
    {
        // Logika untuk menghasilkan laporan PDF akan diimplementasikan sesuai kebutuhan

        return redirect()->route('risk-management.reports.show', $type)
            ->with('success', 'Laporan berhasil disiapkan untuk diunduh');
    }

    /**
     * Generate an Excel report.
     */
    public function generateExcel(Request $request, string $type)
    {
        // Logika untuk menghasilkan laporan Excel akan diimplementasikan sesuai kebutuhan

        return redirect()->route('risk-management.reports.show', $type)
            ->with('success', 'Laporan berhasil disiapkan untuk diunduh');
    }
}
