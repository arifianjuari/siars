<?php

namespace App\Http\Controllers\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentSignature;
use App\Models\DocumentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrValidationController extends Controller
{
    /**
     * Verifikasi QR code
     */
    public function verify($code)
    {
        // Cari tanda tangan berdasarkan hash
        $signature = DocumentSignature::where('signature_hash', $code)->first();

        if (!$signature) {
            return view('document-management.qr-validation.invalid', [
                'message' => 'QR code tidak valid atau tidak ditemukan'
            ]);
        }

        // Load dokumen dan data terkait
        $document = Document::with(['creator', 'memo', 'invitation', 'meetingMinutes'])
            ->findOrFail($signature->document_id);

        // Catat riwayat verifikasi jika user terautentikasi
        if (Auth::check()) {
            DocumentHistory::create([
                'document_id' => $document->id,
                'action' => 'verified',
                'description' => 'Dokumen diverifikasi melalui QR code',
                'performed_by' => Auth::id(),
            ]);
        }

        return view('document-management.qr-validation.valid', [
            'document' => $document,
            'signature' => $signature
        ]);
    }
}
