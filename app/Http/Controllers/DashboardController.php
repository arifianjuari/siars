<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard sesuai role user
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->roles->pluck('name')->first();

        // Cek apakah superadmin sedang impersonating admin RS
        if ($role === 'Superadmin' && session('impersonating_admin')) {
            // Tampilkan dashboard admin RS (manajemen strategis)
            return $this->manajemenStrategisDashboard();
        }

        // Jika user memiliki tenant_id (bukan superadmin), persiapkan data modul aktif
        $activatedModules = collect();
        if ($user->tenant_id) {
            $activatedModules = $user->tenant->modules()
                ->wherePivot('is_active', true)
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at')
                ->get();
        }

        // Redirect berdasarkan role pengguna
        switch ($role) {
            case 'Superadmin':
                // Gunakan view 'dashboard' untuk menampilkan tampilan superadmin
                return view('dashboard', ['user' => $user, 'activatedModules' => $activatedModules]);
            case 'TenantAdmin':
                // TenantAdmin menggunakan dashboard.blade.php utama
                return view('dashboard', ['user' => $user, 'activatedModules' => $activatedModules]);
            case 'ManajemenStrategis':
                return $this->manajemenStrategisDashboard($activatedModules);
            case 'ManajemenEksekutif':
                return $this->manajemenEksekutifDashboard($activatedModules);
            case 'ManajemenOperasional':
                return $this->manajemenOperasionalDashboard($activatedModules);
            case 'Staf':
                return $this->stafDashboard($activatedModules);
            default:
                return view('dashboard', ['user' => $user, 'activatedModules' => $activatedModules]);
        }
    }

    /**
     * Dashboard untuk Superadmin
     */
    public function superadminDashboard()
    {
        return view('dashboard.superadmin');
    }

    /**
     * Dashboard untuk Manajemen Strategis
     */
    public function manajemenStrategisDashboard($activatedModules = null)
    {
        if ($activatedModules === null && Auth::user()->tenant_id) {
            $activatedModules = Auth::user()->tenant->modules()
                ->wherePivot('is_active', true)
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at')
                ->get();
        }

        return view('dashboard.manajemen-strategis', compact('activatedModules'));
    }

    /**
     * Dashboard untuk Manajemen Eksekutif
     */
    public function manajemenEksekutifDashboard($activatedModules = null)
    {
        if ($activatedModules === null && Auth::user()->tenant_id) {
            $activatedModules = Auth::user()->tenant->modules()
                ->wherePivot('is_active', true)
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at')
                ->get();
        }

        return view('dashboard.manajemen-eksekutif', compact('activatedModules'));
    }

    /**
     * Dashboard untuk Manajemen Operasional
     */
    public function manajemenOperasionalDashboard($activatedModules = null)
    {
        if ($activatedModules === null && Auth::user()->tenant_id) {
            $activatedModules = Auth::user()->tenant->modules()
                ->wherePivot('is_active', true)
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at')
                ->get();
        }

        return view('dashboard.manajemen-operasional', compact('activatedModules'));
    }

    /**
     * Dashboard untuk Staf
     */
    public function stafDashboard($activatedModules = null)
    {
        if ($activatedModules === null && Auth::user()->tenant_id) {
            $activatedModules = Auth::user()->tenant->modules()
                ->wherePivot('is_active', true)
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at')
                ->get();
        }

        return view('dashboard.staf', compact('activatedModules'));
    }
}
