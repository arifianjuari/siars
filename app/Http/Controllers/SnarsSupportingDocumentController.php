<?php

namespace App\Http\Controllers;

use App\Models\SnarsSupportingDocument;
use App\Models\SnarsAssessmentElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SnarsSupportingDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsSupportingDocument::query();

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        if ($request->has('element_id')) {
            $elementId = $request->input('element_id');
            $query->where('assessment_element_id', $elementId);
        }

        if ($request->has('latest_only') && $request->input('latest_only') === 'true') {
            $query->latestVersion();
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $documents = $query->with(['assessmentElement', 'creator', 'approver'])->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $documents->items(),
                'pagination' => [
                    'total' => $documents->total(),
                    'per_page' => $documents->perPage(),
                    'current_page' => $documents->currentPage(),
                    'last_page' => $documents->lastPage(),
                ],
            ]);
        }

        return view('snars.supporting-documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $elementId = $request->input('element_id');
        $element = null;
        
        if ($elementId) {
            $element = SnarsAssessmentElement::findOrFail($elementId);
        }
        
        return view('snars.supporting-documents.create', compact('element'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assessment_element_id' => 'required|string|exists:snars_assessment_elements,id',
            'document_file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        
        // Handle file upload
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('supporting-documents', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getMimeType();
            $data['file_size'] = $file->getSize();
        }

        // Set additional fields
        $data['status'] = 'draft';
        $data['version'] = 1;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $document = SnarsSupportingDocument::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $document, 'message' => 'Document uploaded successfully'], 201);
        }

        return redirect()->route('snars.supporting-documents.show', $document->id)
            ->with('success', 'Document uploaded successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $document = SnarsSupportingDocument::with(['assessmentElement', 'creator', 'approver', 'parent', 'children'])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $document]);
        }

        return view('snars.supporting-documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $document = SnarsSupportingDocument::findOrFail($id);
        
        // Only draft documents can be edited
        if ($document->status !== 'draft') {
            return redirect()->route('snars.supporting-documents.show', $document->id)
                ->with('error', 'Only draft documents can be edited');
        }
        
        return view('snars.supporting-documents.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $document = SnarsSupportingDocument::findOrFail($id);
        
        // Only draft documents can be updated
        if ($document->status !== 'draft') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Only draft documents can be updated'], 422);
            }
            return redirect()->route('snars.supporting-documents.show', $document->id)
                ->with('error', 'Only draft documents can be updated');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

        // Handle file upload if a new file is provided
        if ($request->hasFile('document_file')) {
            // Delete old file
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('supporting-documents', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getMimeType();
            $data['file_size'] = $file->getSize();
        }

        $document->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $document, 'message' => 'Document updated successfully']);
        }

        return redirect()->route('snars.supporting-documents.show', $document->id)
            ->with('success', 'Document updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $document = SnarsSupportingDocument::findOrFail($id);
        
        // Check if the document has child versions
        if ($document->children()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete document because it has newer versions.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete document because it has newer versions.');
        }

        // Delete the file from storage
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Document deleted successfully']);
        }

        return redirect()->route('snars.supporting-documents.index')
            ->with('success', 'Document deleted successfully');
    }
    
    /**
     * Submit a document for review.
     */
    public function submitForReview(string $id)
    {
        $document = SnarsSupportingDocument::findOrFail($id);
        
        // Only draft documents can be submitted for review
        if ($document->status !== 'draft') {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Only draft documents can be submitted for review'], 422);
            }
            return redirect()->back()->with('error', 'Only draft documents can be submitted for review');
        }
        
        $document->update([
            'status' => 'review',
            'updated_by' => Auth::id(),
        ]);
        
        if (request()->expectsJson()) {
            return response()->json(['data' => $document, 'message' => 'Document submitted for review']);
        }
        
        return redirect()->route('snars.supporting-documents.show', $document->id)
            ->with('success', 'Document submitted for review');
    }
    
    /**
     * Approve a document.
     */
    public function approve(Request $request, string $id)
    {
        $document = SnarsSupportingDocument::findOrFail($id);
        
        // Only documents under review can be approved
        if ($document->status !== 'review') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Only documents under review can be approved'], 422);
            }
            return redirect()->back()->with('error', 'Only documents under review can be approved');
        }
        
        $document->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'updated_by' => Auth::id(),
        ]);
        
        if ($request->expectsJson()) {
            return response()->json(['data' => $document, 'message' => 'Document approved']);
        }
        
        return redirect()->route('snars.supporting-documents.show', $document->id)
            ->with('success', 'Document approved');
    }
    
    /**
     * Reject a document.
     */
    public function reject(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $document = SnarsSupportingDocument::findOrFail($id);
        
        // Only documents under review can be rejected
        if ($document->status !== 'review') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Only documents under review can be rejected'], 422);
            }
            return redirect()->back()->with('error', 'Only documents under review can be rejected');
        }
        
        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
            'updated_by' => Auth::id(),
        ]);
        
        if ($request->expectsJson()) {
            return response()->json(['data' => $document, 'message' => 'Document rejected']);
        }
        
        return redirect()->route('snars.supporting-documents.show', $document->id)
            ->with('success', 'Document rejected');
    }
    
    /**
     * Create a new version of a document.
     */
    public function createNewVersion(string $id)
    {
        $document = SnarsSupportingDocument::findOrFail($id);
        
        return view('snars.supporting-documents.create-version', compact('document'));
    }
    
    /**
     * Store a new version of a document.
     */
    public function storeNewVersion(Request $request, string $id)
    {
        $parentDocument = SnarsSupportingDocument::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        
        // Handle file upload
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('supporting-documents', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getMimeType();
            $data['file_size'] = $file->getSize();
        }

        // Set additional fields
        $data['assessment_element_id'] = $parentDocument->assessment_element_id;
        $data['parent_id'] = $parentDocument->id;
        $data['status'] = 'draft';
        $data['version'] = $parentDocument->version + 1;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $document = SnarsSupportingDocument::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $document, 'message' => 'New version created successfully'], 201);
        }

        return redirect()->route('snars.supporting-documents.show', $document->id)
            ->with('success', 'New version created successfully');
    }
    
    /**
     * Download a document.
     */
    public function download(string $id)
    {
        $document = SnarsSupportingDocument::findOrFail($id);
        
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File not found');
        }
        
        return response()->download(Storage::disk('public')->path($document->file_path), $document->file_name);
    }
    
    /**
     * View document history (all versions).
     */
    public function history(string $id)
    {
        $document = SnarsSupportingDocument::findOrFail($id);
        
        // Find the root document (first version)
        $rootDocument = $document;
        while ($rootDocument->parent_id) {
            $rootDocument = $rootDocument->parent;
        }
        
        // Get all versions in order
        $versions = collect([$rootDocument]);
        $currentDoc = $rootDocument;
        
        while ($child = $currentDoc->children()->first()) {
            $versions->push($child);
            $currentDoc = $child;
        }
        
        if (request()->expectsJson()) {
            return response()->json(['data' => $versions]);
        }
        
        return view('snars.supporting-documents.history', [
            'document' => $document,
            'versions' => $versions
        ]);
    }
}
