<?php

namespace App\Http\Controllers;

use App\Models\SnarsDocument;
use App\Models\SnarsDocumentType;
use App\Models\SnarsAssessmentElement;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SnarsDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsDocument::with(['documentType', 'department', 'assessmentElements']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->has('document_type_id')) {
            $query->where('document_type_id', $request->input('document_type_id'));
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('element_id')) {
            $elementId = $request->input('element_id');
            $query->whereHas('assessmentElements', function ($q) use ($elementId) {
                $q->where('snars_assessment_elements.id', $elementId);
            });
        }

        // Filter by expiry date range
        if ($request->has('expiry_from') && $request->has('expiry_to')) {
            $query->whereBetween('expiry_date', [$request->input('expiry_from'), $request->input('expiry_to')]);
        } elseif ($request->has('expiry_from')) {
            $query->whereDate('expiry_date', '>=', $request->input('expiry_from'));
        } elseif ($request->has('expiry_to')) {
            $query->whereDate('expiry_date', '<=', $request->input('expiry_to'));
        }

        // Special filters
        if ($request->has('filter')) {
            $filter = $request->input('filter');
            
            if ($filter === 'expiring_soon') {
                $days = $request->input('days', 30);
                $query->expiringSoon($days);
            } elseif ($filter === 'expired') {
                $query->expired();
            }
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $documents = $query->paginate($perPage);

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

        $documentTypes = SnarsDocumentType::active()->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $elements = SnarsAssessmentElement::with(['standard', 'standard.chapter', 'standard.chapter.group'])->active()->ordered()->get();
        
        return view('snars.documents.index', compact('documents', 'documentTypes', 'departments', 'elements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $documentTypes = SnarsDocumentType::active()->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $elements = SnarsAssessmentElement::with(['standard', 'standard.chapter', 'standard.chapter.group'])->active()->ordered()->get();
        
        return view('snars.documents.create', compact('documentTypes', 'departments', 'elements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type_id' => 'required|string|exists:snars_document_types,id',
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
            'version' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,review,approved,archived',
            'approval_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:approval_date',
            'notes' => 'nullable|string',
            'assessment_elements' => 'nullable|array',
            'assessment_elements.*' => 'exists:snars_assessment_elements,id',
            'is_primary' => 'nullable|array',
            'is_primary.*' => 'boolean',
            'element_notes' => 'nullable|array',
            'element_notes.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle file upload
        $file = $request->file('document_file');
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileExtension = $file->getClientOriginalExtension();
        
        // Generate a unique file path
        $filePath = 'documents/' . date('Y/m/d') . '/' . Str::uuid() . '.' . $fileExtension;
        
        // Store the file
        Storage::disk('public')->put($filePath, file_get_contents($file));

        // Create document record
        $data = $validator->validated();
        $data['file_path'] = $filePath;
        $data['file_name'] = $fileName;
        $data['file_size'] = $fileSize;
        $data['file_extension'] = $fileExtension;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['tenant_id'] = session('tenant_id'); // Assuming tenant_id is stored in session
        
        // Set default version if not provided
        if (!isset($data['version'])) {
            $data['version'] = 1.0;
        }
        
        // Remove unnecessary fields
        unset($data['document_file']);
        unset($data['assessment_elements']);
        unset($data['is_primary']);
        unset($data['element_notes']);

        $document = SnarsDocument::create($data);

        // Sync assessment elements if provided
        if ($request->has('assessment_elements')) {
            $pivotData = [];
            foreach ($request->input('assessment_elements') as $index => $elementId) {
                $isPrimary = isset($request->input('is_primary')[$index]) ? true : false;
                $notes = $request->input('element_notes')[$index] ?? null;
                
                $pivotData[$elementId] = [
                    'is_primary' => $isPrimary,
                    'notes' => $notes,
                ];
            }
            
            $document->assessmentElements()->sync($pivotData);
        }

        if ($request->expectsJson()) {
            return response()->json(['data' => $document, 'message' => 'Document created successfully'], 201);
        }

        return redirect()->route('snars.documents.index')
            ->with('success', 'Document created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $document = SnarsDocument::with([
            'documentType', 
            'department', 
            'assessmentElements', 
            'assessmentElements.standard', 
            'assessmentElements.standard.chapter', 
            'assessmentElements.standard.chapter.group'
        ])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $document]);
        }

        return view('snars.documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $document = SnarsDocument::with(['assessmentElements'])->findOrFail($id);
        $documentTypes = SnarsDocumentType::active()->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $elements = SnarsAssessmentElement::with(['standard', 'standard.chapter', 'standard.chapter.group'])->active()->ordered()->get();
        
        return view('snars.documents.edit', compact('document', 'documentTypes', 'departments', 'elements'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $document = SnarsDocument::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'document_type_id' => 'required|string|exists:snars_document_types,id',
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
            'version' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,review,approved,archived',
            'approval_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:approval_date',
            'notes' => 'nullable|string',
            'assessment_elements' => 'nullable|array',
            'assessment_elements.*' => 'exists:snars_assessment_elements,id',
            'is_primary' => 'nullable|array',
            'is_primary.*' => 'boolean',
            'element_notes' => 'nullable|array',
            'element_notes.*' => 'nullable|string',
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
            // Delete the old file
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // Process the new file
            $file = $request->file('document_file');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileExtension = $file->getClientOriginalExtension();
            
            // Generate a unique file path
            $filePath = 'documents/' . date('Y/m/d') . '/' . Str::uuid() . '.' . $fileExtension;
            
            // Store the file
            Storage::disk('public')->put($filePath, file_get_contents($file));
            
            // Update document data
            $data['file_path'] = $filePath;
            $data['file_name'] = $fileName;
            $data['file_size'] = $fileSize;
            $data['file_extension'] = $fileExtension;
        }
        
        // Set default version if not provided
        if (!isset($data['version'])) {
            $data['version'] = $document->version;
        }
        
        // Remove unnecessary fields
        unset($data['document_file']);
        unset($data['assessment_elements']);
        unset($data['is_primary']);
        unset($data['element_notes']);

        $document->update($data);

        // Sync assessment elements if provided
        if ($request->has('assessment_elements')) {
            $pivotData = [];
            foreach ($request->input('assessment_elements') as $index => $elementId) {
                $isPrimary = isset($request->input('is_primary')[$index]) ? true : false;
                $notes = $request->input('element_notes')[$index] ?? null;
                
                $pivotData[$elementId] = [
                    'is_primary' => $isPrimary,
                    'notes' => $notes,
                ];
            }
            
            $document->assessmentElements()->sync($pivotData);
        } else {
            // If no elements are provided, detach all
            $document->assessmentElements()->detach();
        }

        if ($request->expectsJson()) {
            return response()->json(['data' => $document, 'message' => 'Document updated successfully']);
        }

        return redirect()->route('snars.documents.index')
            ->with('success', 'Document updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $document = SnarsDocument::findOrFail($id);
        
        // Delete the file from storage
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        // Detach all assessment elements
        $document->assessmentElements()->detach();
        
        // Delete the document record
        $document->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Document deleted successfully']);
        }

        return redirect()->route('snars.documents.index')
            ->with('success', 'Document deleted successfully');
    }

    /**
     * Download the document file.
     */
    public function download(string $id)
    {
        $document = SnarsDocument::findOrFail($id);
        
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'File not found'], 404);
            }
            return redirect()->back()->with('error', 'File not found');
        }
        
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Update the document status.
     */
    public function updateStatus(Request $request, string $id)
    {
        $document = SnarsDocument::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,review,approved,archived',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $document->update([
            'status' => $request->input('status'),
            'updated_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['data' => $document, 'message' => 'Document status updated successfully']);
        }

        return redirect()->back()
            ->with('success', 'Document status updated successfully');
    }
}
