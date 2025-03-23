<?php

namespace App\Http\Controllers;

use App\Models\SnarsRequiredDocument;
use App\Models\SnarsAssessmentElement;
use App\Models\SnarsDocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SnarsRequiredDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsRequiredDocument::with(['assessmentElement', 'documentType', 'assessmentElement.standard', 'assessmentElement.standard.chapter', 'assessmentElement.standard.chapter.group']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%");
            });
        }

        if ($request->has('element_id')) {
            $query->where('element_id', $request->input('element_id'));
        }

        if ($request->has('document_type_id')) {
            $query->where('document_type_id', $request->input('document_type_id'));
        }

        if ($request->has('standard_id')) {
            $elementIds = SnarsAssessmentElement::where('standard_id', $request->input('standard_id'))->pluck('id');
            $query->whereIn('element_id', $elementIds);
        }

        if ($request->has('is_mandatory')) {
            $isMandatory = filter_var($request->input('is_mandatory'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_mandatory', $isMandatory);
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $requiredDocuments = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $requiredDocuments->items(),
                'pagination' => [
                    'total' => $requiredDocuments->total(),
                    'per_page' => $requiredDocuments->perPage(),
                    'current_page' => $requiredDocuments->currentPage(),
                    'last_page' => $requiredDocuments->lastPage(),
                ],
            ]);
        }

        $elements = SnarsAssessmentElement::with(['standard', 'standard.chapter', 'standard.chapter.group'])->active()->ordered()->get();
        $documentTypes = SnarsDocumentType::active()->orderBy('name')->get();
        return view('snars.required-documents.index', compact('requiredDocuments', 'elements', 'documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $elements = SnarsAssessmentElement::with(['standard', 'standard.chapter', 'standard.chapter.group'])->active()->ordered()->get();
        $documentTypes = SnarsDocumentType::active()->orderBy('name')->get();
        return view('snars.required-documents.create', compact('elements', 'documentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'element_id' => 'required|string|exists:snars_assessment_elements,id',
            'document_type_id' => 'required|string|exists:snars_document_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'is_mandatory' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        // Set default values if not provided
        if (!isset($data['is_mandatory'])) {
            $data['is_mandatory'] = true;
        }

        $requiredDocument = SnarsRequiredDocument::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $requiredDocument, 'message' => 'Required document created successfully'], 201);
        }

        return redirect()->route('snars.required-documents.index')
            ->with('success', 'Required document created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $requiredDocument = SnarsRequiredDocument::with([
            'assessmentElement', 
            'documentType', 
            'assessmentElement.standard', 
            'assessmentElement.standard.chapter', 
            'assessmentElement.standard.chapter.group'
        ])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $requiredDocument]);
        }

        return view('snars.required-documents.show', compact('requiredDocument'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $requiredDocument = SnarsRequiredDocument::findOrFail($id);
        $elements = SnarsAssessmentElement::with(['standard', 'standard.chapter', 'standard.chapter.group'])->active()->ordered()->get();
        $documentTypes = SnarsDocumentType::active()->orderBy('name')->get();
        return view('snars.required-documents.edit', compact('requiredDocument', 'elements', 'documentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $requiredDocument = SnarsRequiredDocument::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'element_id' => 'required|string|exists:snars_assessment_elements,id',
            'document_type_id' => 'required|string|exists:snars_document_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'is_mandatory' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

        // Set default values if not provided
        if (!isset($data['is_mandatory'])) {
            $data['is_mandatory'] = $requiredDocument->is_mandatory;
        }

        $requiredDocument->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $requiredDocument, 'message' => 'Required document updated successfully']);
        }

        return redirect()->route('snars.required-documents.index')
            ->with('success', 'Required document updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $requiredDocument = SnarsRequiredDocument::findOrFail($id);
        $requiredDocument->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Required document deleted successfully']);
        }

        return redirect()->route('snars.required-documents.index')
            ->with('success', 'Required document deleted successfully');
    }

    /**
     * Toggle the mandatory status of a required document.
     */
    public function toggleMandatory(string $id)
    {
        $requiredDocument = SnarsRequiredDocument::findOrFail($id);
        $requiredDocument->update([
            'is_mandatory' => !$requiredDocument->is_mandatory,
            'updated_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $requiredDocument,
                'message' => 'Required document mandatory status updated successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Required document mandatory status updated successfully');
    }
}
