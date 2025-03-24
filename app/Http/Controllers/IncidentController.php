<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\IncidentSubtype;
use App\Models\Location;
use App\Models\User;
use App\Models\Profession;
use App\Models\RiskClassification;
use App\Models\RootCauseAnalysis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\SimpleType\Jc;
use Mpdf\Mpdf;
use Illuminate\Support\Str;

class IncidentController extends Controller
{
    /**
     * Display a listing of the incidents.
     */
    public function index(Request $request)
    {
        $query = Incident::with(['location', 'incidentType', 'incidentSubtype', 'reporter'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_waktu_kejadian', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan lokasi
        if ($request->has('location_id') && $request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        // Filter berdasarkan jenis insiden
        if ($request->has('incident_type_id') && $request->incident_type_id) {
            $query->where('incident_type_id', $request->incident_type_id);
        }

        // Filter berdasarkan level risiko
        if ($request->has('risk_level') && $request->risk_level) {
            $query->whereHas('classification', function ($q) use ($request) {
                $q->where('level_risiko', $request->risk_level);
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan keyword
        if ($request->has('keyword') && $request->keyword) {
            $keyword = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('no_rm', 'like', $keyword)
                    ->orWhere('nama_pasien', 'like', $keyword)
                    ->orWhere('kronologis', 'like', $keyword);
            });
        }

        $incidents = $query->paginate(10);
        $locations = Location::where('is_active', true)->get();
        $incidentTypes = IncidentType::where('is_active', true)->get();

        return view('risk-management.incidents.index', compact('incidents', 'locations', 'incidentTypes'));
    }

    /**
     * Show the form for creating a new incident.
     */
    public function create()
    {
        $locations = Location::where('is_active', true)->get();
        $incidentTypes = IncidentType::where('is_active', true)->get();
        $professions = DB::table('professions')->where('is_active', true)->get();

        return view('risk-management.incidents.create', compact('locations', 'incidentTypes', 'professions'));
    }

    /**
     * Store a newly created incident in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_waktu_kejadian' => 'required|date',
            'lokasi_id' => 'required|exists:locations,id',
            'jenis_insiden_id' => 'required|exists:incident_types,id',
            'incident_subtype_id' => 'nullable|exists:incident_subtypes,id',
            'nama_pasien' => 'required|string|max:255',
            'no_rekam_medis' => 'required|string|max:50',
            'kronologis' => 'required|string',
            'tindakan_langsung' => 'required|string',
            'nama_pelapor' => 'required|string|max:255',
            'profesi_id' => 'required|exists:professions,id',
            'pernah_terjadi_sebelumnya' => 'required|boolean',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        // Generate nomor kasus berdasarkan tanggal kejadian dan nomor urut
        $today = now()->format('Ymd');
        $latestIncident = Incident::where('case_number', 'like', "$today-%")->orderBy('id', 'desc')->first();

        if ($latestIncident) {
            // Jika ada insiden dengan awalan tanggal yang sama, ambil nomor urut terakhir dan tambahkan 1
            $lastNumber = (int) explode('-', $latestIncident->case_number)[1];
            $newNumber = $lastNumber + 1;
        } else {
            // Jika tidak ada insiden dengan tanggal yang sama, mulai dari 1
            $newNumber = 1;
        }

        $caseNumber = $today . '-' . $newNumber;

        // Menyesuaikan nama field ke kolom database
        $data = [
            'case_number' => $caseNumber,
            'tanggal_waktu_kejadian' => $validated['tanggal_waktu_kejadian'],
            'location_id' => $validated['lokasi_id'],
            'incident_type_id' => $validated['jenis_insiden_id'],
            'incident_subtype_id' => $validated['incident_subtype_id'],
            'nama_pasien' => $validated['nama_pasien'],
            'no_rm' => $validated['no_rekam_medis'],
            'kronologis' => $validated['kronologis'],
            'tindakan_langsung' => $validated['tindakan_langsung'],
            'nama_pelapor' => $validated['nama_pelapor'],
            'profesi_id' => $validated['profesi_id'],
            'pernah_terjadi_sebelumnya' => $validated['pernah_terjadi_sebelumnya'],
            'reporter_id' => Auth::id(),
            'status' => 'Baru'
        ];

        // Upload dokumen pendukung jika ada
        if ($request->hasFile('dokumen_pendukung')) {
            $path = $request->file('dokumen_pendukung')->store('documents/incidents', 'public');
            $data['dokumen_pendukung'] = $path;
        }

        $incident = Incident::create($data);

        return redirect()->route('risk-management.incidents.show', $incident)
            ->with('success', 'Insiden berhasil dilaporkan!');
    }

    /**
     * Display the specified incident.
     */
    public function show(Incident $incident)
    {
        $incident->load(['location', 'incidentType', 'incidentSubtype', 'reporter', 'classification', 'analysis']);

        return view('risk-management.incidents.show', compact('incident'));
    }

    /**
     * Show the form for editing the specified incident.
     */
    public function edit(Incident $incident)
    {
        $locations = Location::where('is_active', true)->get();
        $incidentTypes = IncidentType::where('is_active', true)->get();
        $incidentSubtypes = IncidentSubtype::where('incident_type_id', $incident->incident_type_id)
            ->where('is_active', true)
            ->get();

        return view('risk-management.incidents.edit', compact('incident', 'locations', 'incidentTypes', 'incidentSubtypes'));
    }

    /**
     * Update the specified incident in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'tanggal_waktu_kejadian' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'incident_type_id' => 'required|exists:incident_types,id',
            'incident_subtype_id' => 'nullable|exists:incident_subtypes,id',
            'nama_pasien' => 'required|string|max:255',
            'no_rm' => 'required|string|max:50',
            'kronologis' => 'required|string',
            'tindakan_langsung' => 'required|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        // Upload dokumen pendukung jika ada
        if ($request->hasFile('dokumen_pendukung')) {
            // Hapus file lama jika ada
            if ($incident->dokumen_pendukung) {
                Storage::disk('public')->delete($incident->dokumen_pendukung);
            }

            $path = $request->file('dokumen_pendukung')->store('documents/incidents', 'public');
            $validated['dokumen_pendukung'] = $path;
        }

        $incident->update($validated);

        return redirect()->route('risk-management.incidents.show', $incident)
            ->with('success', 'Insiden berhasil diperbarui!');
    }

    /**
     * Remove the specified incident from storage.
     */
    public function destroy(Incident $incident)
    {
        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Hapus analisis terkait jika ada
            if ($incident->analysis) {
                $incident->analysis->delete();
            }

            // Hapus klasifikasi risiko terkait jika ada
            if ($incident->classification) {
                $incident->classification->delete();
            }

            // Hapus file terkait
            if ($incident->dokumen_pendukung) {
                Storage::delete($incident->dokumen_pendukung);
            }

            if ($incident->dokumen_evaluasi) {
                Storage::delete($incident->dokumen_evaluasi);
            }

            if ($incident->handling_document) {
                Storage::delete('public/documents/incidents/' . $incident->handling_document);
            }

            // Hapus insiden
            $incident->delete();

            // Commit transaksi
            DB::commit();

            return redirect()->route('risk-management.incidents.index')
                ->with('success', 'Insiden berhasil dihapus!');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            // Log error
            Log::error('Error saat menghapus insiden: ' . $e->getMessage());

            return redirect()->route('risk-management.incidents.show', $incident)
                ->with('error', 'Terjadi kesalahan saat menghapus insiden. Silakan coba lagi.');
        }
    }

    /**
     * Ambil subtypes berdasarkan incident type
     */
    public function getSubtypes(Request $request)
    {
        try {
            // Debug request
            info('Request getSubtypes: ' . json_encode([
                'incident_type_id' => $request->incident_type_id,
                'all_params' => $request->all(),
                'headers' => $request->header(),
                'url' => $request->fullUrl(),
                'is_ajax' => $request->ajax()
            ]));

            if (!$request->has('incident_type_id') || !$request->incident_type_id) {
                info('ID jenis insiden tidak valid');
                return response()->json(['error' => 'ID jenis insiden tidak valid'], 400);
            }

            $subtypes = IncidentSubtype::where('incident_type_id', $request->incident_type_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'description', 'is_active']);

            // Debug response
            info('Subtypes found: ' . $subtypes->count() . ', data: ' . json_encode($subtypes));

            return response()->json($subtypes)
                ->header('Content-Type', 'application/json')
                ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
        } catch (\Exception $e) {
            // Log error
            info('Error in getSubtypes: ' . $e->getMessage());

            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hapus dokumen pendukung dari insiden.
     */
    public function deleteDocument(Incident $incident)
    {
        // Cek apakah ada dokumen pendukung
        if ($incident->dokumen_pendukung) {
            // Hapus file
            Storage::disk('public')->delete($incident->dokumen_pendukung);

            // Update record di database
            $incident->update(['dokumen_pendukung' => null]);

            return redirect()->route('risk-management.incidents.show', $incident)
                ->with('success', 'Dokumen pendukung berhasil dihapus.');
        }

        return redirect()->route('risk-management.incidents.show', $incident)
            ->with('error', 'Tidak ada dokumen pendukung untuk dihapus.');
    }

    /**
     * Mengekspor data insiden ke dalam file PDF
     */
    public function exportPdf(Incident $incident)
    {
        // Inisialisasi mPDF
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'default_font' => 'arial',
        ]);

        // Set judul dokumen
        $mpdf->SetTitle('Laporan Insiden Keselamatan Pasien');

        // CSS untuk styling
        $stylesheet = '
            body { font-family: Arial, sans-serif; font-size: 11pt; }
            h1 { font-size: 14pt; text-align: center; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; }
            h2 { font-size: 12pt; font-weight: bold; margin-top: 20px; margin-bottom: 10px; }
            .label { font-weight: bold; width: 35%; vertical-align: top; font-size: 11pt; }
            .data { width: 65%; vertical-align: top; font-size: 11pt; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            table.no-border td { padding: 5px 0; }
            .signature { margin-top: 30px; }
            .signature-line { border-bottom: 1px solid black; width: 200px; margin-top: 40px; }
            p { font-size: 11pt; }
        ';

        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        // Memulai konten HTML
        $html = '
        <h1>LAPORAN INSIDEN KESELAMATAN PASIEN</h1>
        
        <h2>I. IDENTIFIKASI INSIDEN</h2>
        <table class="no-border">
            <tr>
                <td class="label">Tanggal dan Waktu Kejadian</td>
                <td class="data">: ' . $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') . '</td>
            </tr>
            <tr>
                <td class="label">Lokasi Kejadian</td>
                <td class="data">: ' . $incident->location->name . '</td>
            </tr>
            <tr>
                <td class="label">Nama Pasien/Inisial</td>
                <td class="data">: ' . $incident->nama_pasien . '</td>
            </tr>
            <tr>
                <td class="label">Nomor Rekam Medis</td>
                <td class="data">: ' . $incident->no_rm . '</td>
            </tr>
            <tr>
                <td class="label">Jenis Insiden</td>
                <td class="data">: ' . $incident->incidentType->name . '</td>
            </tr>';

        if ($incident->incidentSubtype) {
            $html .= '
            <tr>
                <td class="label">Subtipe Insiden</td>
                <td class="data">: ' . $incident->incidentSubtype->name . '</td>
            </tr>';
        }

        $html .= '
            <tr>
                <td class="label">Kronologis Kejadian</td>
                <td class="data">: ' . $incident->kronologis . '</td>
            </tr>
            <tr>
                <td class="label">Tindakan Langsung</td>
                <td class="data">: ' . $incident->tindakan_langsung . '</td>
            </tr>
            <tr>
                <td class="label">Pelapor</td>
                <td class="data">: ' . $incident->reporter->name . '</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pelaporan</td>
                <td class="data">: ' . $incident->created_at->format('d/m/Y H:i') . '</td>
            </tr>
        </table>';

        // Informasi Klasifikasi Risiko jika tersedia
        if ($incident->classification) {
            $html .= '
            <h2>II. KLASIFIKASI RISIKO</h2>
            <table class="no-border">
                <tr>
                    <td class="label">Dampak</td>
                    <td class="data">: ' . $incident->classification->dampak . ' - ' . $incident->classification->dampak_detail . '</td>
                </tr>
                <tr>
                    <td class="label">Probabilitas</td>
                    <td class="data">: ' . $incident->classification->probabilitas . ' - ' . $incident->classification->probabilitas_detail . '</td>
                </tr>
                <tr>
                    <td class="label">Skor Risiko</td>
                    <td class="data">: ' . $incident->classification->skor_risiko . '</td>
                </tr>
                <tr>
                    <td class="label">Level Risiko</td>
                    <td class="data">: ' . $incident->classification->level_risiko . ' (Zona ' . $incident->classification->zona_risiko . ')</td>
                </tr>
            </table>';
        }

        // Informasi Analisis Akar Masalah jika tersedia
        if ($incident->analysis) {
            $html .= '
            <h2>III. ANALISIS AKAR MASALAH</h2>
            <table class="no-border">
                <tr>
                    <td class="label">Metode Analisis</td>
                    <td class="data">: ' . $incident->analysis->analysis_method . '</td>
                </tr>';

            if ($incident->analysis->team_factors) {
                $html .= '
                <tr>
                    <td class="label">Faktor Tim</td>
                    <td class="data">: ' . $incident->analysis->team_factors . '</td>
                </tr>';
            }

            if ($incident->analysis->system_factors) {
                $html .= '
                <tr>
                    <td class="label">Faktor Sistem</td>
                    <td class="data">: ' . $incident->analysis->system_factors . '</td>
                </tr>';
            }

            if ($incident->analysis->patient_factors) {
                $html .= '
                <tr>
                    <td class="label">Faktor Pasien</td>
                    <td class="data">: ' . $incident->analysis->patient_factors . '</td>
                </tr>';
            }

            if ($incident->analysis->environmental_factors) {
                $html .= '
                <tr>
                    <td class="label">Faktor Lingkungan</td>
                    <td class="data">: ' . $incident->analysis->environmental_factors . '</td>
                </tr>';
            }

            $html .= '
                <tr>
                    <td class="label">Akar Masalah</td>
                    <td class="data">: ' . $incident->analysis->root_causes . '</td>
                </tr>
                <tr>
                    <td class="label">Rekomendasi</td>
                    <td class="data">: ' . $incident->analysis->recommendations . '</td>
                </tr>
            </table>';
        }

        // Informasi Penanganan dan Monitoring jika tersedia
        if ($incident->handling_actions || $incident->handling_date || $incident->handling_result) {
            $html .= '
            <h2>IV. PENANGANAN DAN MONITORING</h2>
            <table class="no-border">';

            if ($incident->handling_date) {
                $html .= '
                <tr>
                    <td class="label">Tanggal Penanganan</td>
                    <td class="data">: ' . $incident->handling_date->format('d/m/Y') . '</td>
                </tr>';
            }

            if ($incident->handling_actions) {
                $html .= '
                <tr>
                    <td class="label">Tindakan Penanganan</td>
                    <td class="data">: ' . $incident->handling_actions . '</td>
                </tr>';
            }

            if ($incident->handling_result) {
                $html .= '
                <tr>
                    <td class="label">Hasil Penanganan</td>
                    <td class="data">: ' . $incident->handling_result . '</td>
                </tr>';
            }

            if ($incident->follow_up_plan) {
                $html .= '
                <tr>
                    <td class="label">Rencana Tindak Lanjut</td>
                    <td class="data">: ' . $incident->follow_up_plan . '</td>
                </tr>';
            }

            $html .= '
                <tr>
                    <td class="label">Status</td>
                    <td class="data">: ' . $incident->status . '</td>
                </tr>';

            if ($incident->completed_at) {
                $html .= '
                <tr>
                    <td class="label">Tanggal Penyelesaian</td>
                    <td class="data">: ' . $incident->completed_at->format('d/m/Y') . '</td>
                </tr>';
            }

            $html .= '
            </table>';
        }

        // Bagian tanda tangan
        $html .= '
        <div class="signature">
            <p><strong>Divalidasi Oleh:</strong></p>';

        // Tambahkan QR code jika tersedia
        if ($incident->qr_code_path && Storage::disk('public')->exists($incident->qr_code_path)) {
            // Jika QR code dalam format SVG
            if (Str::endsWith($incident->qr_code_path, '.svg')) {
                $svgContent = Storage::disk('public')->get($incident->qr_code_path);
                // Konversi SVG ke base64 untuk PDF
                $base64Image = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                $html .= '
                <div style="margin: 10px 0;">
                    <img src="' . $base64Image . '" width="100" height="100" style="display: block;">
                </div>';
            } else {
                // Untuk format image lainnya, gunakan path lengkap
                $imagePath = storage_path('app/public/' . $incident->qr_code_path);
                $html .= '
                <div style="margin: 10px 0;">
                    <img src="' . $imagePath . '" width="100" height="100" style="display: block;">
                </div>';
            }
        } elseif ($incident->qr_code_base64) {
            // Jika tersimpan dalam format base64
            $base64Image = 'data:image/svg+xml;base64,' . $incident->qr_code_base64;
            $html .= '
            <div style="margin: 10px 0;">
                <img src="' . $base64Image . '" width="100" height="100" style="display: block;">
            </div>';
        }

        $html .= '
            <div class="signature-line"></div>
            <p>' . (Auth::user()->position ?? 'Petugas') . '</p>
        </div>';

        // Menulis HTML ke PDF
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        // Nama file untuk download
        $fileName = 'Laporan_Insiden_' . $incident->id . '_' . date('Ymd') . '.pdf';

        // Output PDF sebagai download
        return $mpdf->Output($fileName, \Mpdf\Output\Destination::DOWNLOAD);
    }

    /**
     * Tampilkan dashboard
     */
    public function dashboard()
    {
        // Statistik insiden
        $totalIncidents = Incident::count();
        $newIncidents = Incident::where('status', 'Baru')->count();
        $inProgressIncidents = Incident::whereIn('status', ['Proses', 'Evaluasi'])->count();
        $completedIncidents = Incident::where('status', 'Selesai')->count();

        // Statistik berdasarkan level risiko
        $lowRisks = RiskClassification::where('level_risiko', 'Rendah')->count();
        $mediumRisks = RiskClassification::where('level_risiko', 'Sedang')->count();
        $highRisks = RiskClassification::where('level_risiko', 'Tinggi')->count();
        $extremeRisks = RiskClassification::where('level_risiko', 'Ekstrim')->count();
        $classifiedCount = $lowRisks + $mediumRisks + $highRisks + $extremeRisks;
        $classifiedPercentage = $totalIncidents > 0 ? round(($classifiedCount / $totalIncidents) * 100) : 0;

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
        $monthlyData = Incident::selectRaw('MONTH(tanggal_waktu_kejadian) as month, YEAR(tanggal_waktu_kejadian) as year, COUNT(*) as total')
            ->whereRaw('tanggal_waktu_kejadian >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Data untuk grafik jenis insiden
        $incidentTypeData = Incident::selectRaw('incident_types.name, COUNT(*) as total')
            ->join('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->groupBy('incident_types.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Data untuk grafik lokasi
        $locationData = Incident::selectRaw('locations.name, COUNT(*) as total')
            ->join('locations', 'incidents.location_id', '=', 'locations.id')
            ->groupBy('locations.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Data untuk grafik status penanganan
        $statusData = Incident::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        return view('risk-management.dashboard', compact(
            'totalIncidents',
            'newIncidents',
            'inProgressIncidents',
            'completedIncidents',
            'lowRisks',
            'mediumRisks',
            'highRisks',
            'extremeRisks',
            'classifiedPercentage',
            'recentIncidents',
            'highRiskIncidents',
            'monthlyData',
            'incidentTypeData',
            'locationData',
            'statusData'
        ));
    }

    public function generateQrCode(Incident $incident)
    {
        // Pastikan komponen QR code tersedia
        if (!class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            return redirect()->back()->with('error', 'QR Code generator tidak tersedia, silakan install package simplesoftwareio/simple-qrcode terlebih dahulu.');
        }

        try {
            // Buat URL untuk verifikasi dengan ID
            $qrContent = route('risk-management.incidents.verify', $incident->case_number);

            // Coba metode alternatif menggunakan base64 untuk menghindari kebutuhan imagick
            $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(200) // Ukuran konsisten 200px
                ->margin(1)
                ->generate($qrContent);

            // Path untuk simpan QR
            $qrFileName = 'incident_' . $incident->case_number . '.svg';
            $qrPath = 'qrcodes/incidents/' . $qrFileName;
            $fullPath = storage_path('app/public/' . $qrPath);

            // Pastikan direktori ada
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            // Simpan file SVG
            file_put_contents($fullPath, $qrCodeSvg);

            // Buat juga versi base64 untuk kasus di mana SVG tidak didukung
            $qrCodeBase64 = base64_encode($qrCodeSvg);
            $incident->update([
                'qr_code_path' => $qrPath,
                'qr_code_base64' => $qrCodeBase64
            ]);

            return redirect()->back()->with('success', 'QR Code berhasil dibuat dalam format SVG.');
        } catch (\Exception $e) {
            // Jika gagal dengan SVG, coba dengan format lain yang didukung GD
            try {
                // Gunakan format EPS (tidak memerlukan imagick)
                $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('eps')
                    ->size(200) // Ukuran konsisten 200px
                    ->margin(1)
                    ->generate($qrContent);

                // Path untuk simpan QR
                $qrFileName = 'incident_' . $incident->case_number . '.eps';
                $qrPath = 'qrcodes/incidents/' . $qrFileName;
                $fullPath = storage_path('app/public/' . $qrPath);

                // Pastikan direktori ada
                if (!file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                // Simpan file EPS
                file_put_contents($fullPath, $qrCodeSvg);

                // Update incident dengan path QR
                $incident->update(['qr_code_path' => $qrPath]);

                return redirect()->back()->with('success', 'QR Code berhasil dibuat dalam format EPS.');
            } catch (\Exception $innerException) {
                return redirect()->back()->with('error', 'Gagal membuat QR Code: ' . $e->getMessage() . ' dan ' . $innerException->getMessage());
            }
        }
    }

    public function verify($caseNumber)
    {
        $incident = Incident::where('case_number', $caseNumber)->firstOrFail();

        return view('risk-management.incidents.verify', compact('incident'));
    }
}
