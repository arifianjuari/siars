<?php

namespace App\Http\Controllers\DocumentManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        // Tampilkan dashboard sederhana tanpa query kompleks
        return view('document-management.dashboard', [
            'totalDocuments' => 0,
            'notaDinasMasuk' => 0,
            'notaDinasKeluar' => 0,
            'undangan' => 0,
            'notulensi' => 0,
            'needSignature' => 0,
            'recentDocuments' => [],
            'unreadDocuments' => 0,
            'monthlyData' => [],
            'monthlyLabels' => [],
            'documentTypeData' => [],
            'documentTypeLabels' => [],
            'statusData' => [],
            'statusLabels' => []
        ]);
    }
}
