<?php

namespace App\Http\Controllers;

use App\Models\SnarsAssessmentElement;
use App\Models\SnarsSupportingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SnarsSupportingDocumentCreateController extends Controller
{
    /**
     * Menampilkan form untuk membuat dokumen pendukung baru
     */
    public function index()
    {
        // Ambil semua elemen penilaian untuk dropdown
        $assessmentElements = SnarsAssessmentElement::orderBy('code')->get();
        
        return view('snars.supporting-documents.create-form', compact('assessmentElements'));
    }
    
    /**
     * Menyimpan dokumen pendukung baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assessment_element_id' => 'required|exists:snars_assessment_elements,id',
            'document_file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            // Upload file
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('supporting-documents', $fileName, 'public');
            
            // Nonaktifkan auditing sementara untuk menghindari error
            \OwenIt\Auditing\Models\Audit::disableAuditing();
            
            // Simpan dokumen
            $document = new SnarsSupportingDocument();
            $document->id = (string) \Illuminate\Support\Str::uuid();
            $document->title = $request->title;
            $document->description = $request->description;
            $document->assessment_element_id = $request->assessment_element_id;
            $document->file_name = $fileName;
            $document->file_path = $filePath;
            $document->file_size = $file->getSize();
            $document->file_type = $file->getMimeType();
            $document->version = 1;
            $document->status = 'draft';
            $document->created_by = Auth::id();
            $document->save();
            
            // Aktifkan kembali auditing
            \OwenIt\Auditing\Models\Audit::enableAuditing();
            
            return redirect()->route('snars.supporting-documents.index')
                ->with('success', 'Dokumen pendukung berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Log error
            \Illuminate\Support\Facades\Log::error('Error saat menyimpan dokumen: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan dokumen: ' . $e->getMessage()]);
        }
    }
}
