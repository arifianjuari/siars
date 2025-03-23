<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\Location;
use App\Models\RiskClassification;
use App\Models\RootCauseAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiskManagementController extends Controller
{
    /**
     * Display the risk management dashboard.
     */
    public function dashboard()
    {
        // Statistik insiden
        $totalIncidents = Incident::count();
        $newIncidents = Incident::where('status', 'Baru')->count();
        $ongoingIncidents = Incident::where('status', 'Proses')->count();
        $completedIncidents = Incident::where('status', 'Selesai')->count();

        // Statistik level risiko
        $lowRiskCount = RiskClassification::where('level_risiko', 'Rendah')->count();
        $mediumRiskCount = RiskClassification::where('level_risiko', 'Sedang')->count();
        $highRiskCount = RiskClassification::where('level_risiko', 'Tinggi')->count();
        $extremeRiskCount = RiskClassification::where('level_risiko', 'Ekstrim')->count();
        $totalClassifiedIncidents = $lowRiskCount + $mediumRiskCount + $highRiskCount + $extremeRiskCount;

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
        $monthlyData = [];
        $monthlyLabels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M');
            $year = $date->format('Y');
            $monthlyLabels[] = "$month $year";

            $count = Incident::whereYear('tanggal_waktu_kejadian', $date->year)
                ->whereMonth('tanggal_waktu_kejadian', $date->month)
                ->count();

            $monthlyData[] = $count;
        }

        // Data untuk grafik jenis insiden
        $incidentTypeData = DB::table('incidents')
            ->join('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->select('incident_types.name', DB::raw('count(*) as total'))
            ->groupBy('incident_types.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->pluck('total')
            ->toArray();

        $incidentTypeLabels = DB::table('incidents')
            ->join('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->select('incident_types.name', DB::raw('count(*) as total'))
            ->groupBy('incident_types.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->pluck('name')
            ->toArray();

        // Data untuk grafik lokasi
        $locationData = DB::table('incidents')
            ->join('locations', 'incidents.location_id', '=', 'locations.id')
            ->select('locations.name', DB::raw('count(*) as total'))
            ->groupBy('locations.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->pluck('total')
            ->toArray();

        $locationLabels = DB::table('incidents')
            ->join('locations', 'incidents.location_id', '=', 'locations.id')
            ->select('locations.name', DB::raw('count(*) as total'))
            ->groupBy('locations.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->pluck('name')
            ->toArray();

        // Data untuk grafik status
        $statusData = DB::table('incidents')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total')
            ->toArray();

        $statusLabels = DB::table('incidents')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('status')
            ->toArray();

        return view('risk-management.dashboard', compact(
            'totalIncidents',
            'newIncidents',
            'ongoingIncidents',
            'completedIncidents',
            'lowRiskCount',
            'mediumRiskCount',
            'highRiskCount',
            'extremeRiskCount',
            'totalClassifiedIncidents',
            'recentIncidents',
            'highRiskIncidents',
            'monthlyData',
            'monthlyLabels',
            'incidentTypeData',
            'incidentTypeLabels',
            'locationData',
            'locationLabels',
            'statusData',
            'statusLabels'
        ));
    }
}
