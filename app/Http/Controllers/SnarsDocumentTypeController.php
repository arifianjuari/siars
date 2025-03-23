<?php

namespace App\Http\Controllers;

use App\Models\SnarsDocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SnarsDocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsDocumentType::query();

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('format_requirements', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $documentTypes = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $documentTypes->items(),
                'pagination' => [
                    'total' => $documentTypes->total(),
                    'per_page' => $documentTypes->perPage(),
                    'current_page' => $documentTypes->currentPage(),
                    'last_page' => $documentTypes->lastPage(),
                ],
            ]);
        }

        return view('snars.document-types.index', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('snars.document-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:snars_document_types,name',
            'description' => 'nullable|string',
            'format_requirements' => 'nullable|string',
            'retention_period' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
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
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        $documentType = SnarsDocumentType::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $documentType, 'message' => 'Document type created successfully'], 201);
        }

        return redirect()->route('snars.document-types.index')
            ->with('success', 'Document type created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $documentType = SnarsDocumentType::with(['requiredDocuments', 'documents'])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $documentType]);
        }

        return view('snars.document-types.show', compact('documentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $documentType = SnarsDocumentType::findOrFail($id);
        return view('snars.document-types.edit', compact('documentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $documentType = SnarsDocumentType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('snars_document_types', 'name')->ignore($documentType->id)],
            'description' => 'nullable|string',
            'format_requirements' => 'nullable|string',
            'retention_period' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
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
        if (!isset($data['is_active'])) {
            $data['is_active'] = $documentType->is_active;
        }

        $documentType->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $documentType, 'message' => 'Document type updated successfully']);
        }

        return redirect()->route('snars.document-types.index')
            ->with('success', 'Document type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $documentType = SnarsDocumentType::findOrFail($id);

        // Check if the document type has any required documents
        if ($documentType->requiredDocuments()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete document type because it has associated required documents. Remove the required documents first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete document type because it has associated required documents. Remove the required documents first.');
        }

        // Check if the document type has any documents
        if ($documentType->documents()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete document type because it has associated documents. Remove the documents first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete document type because it has associated documents. Remove the documents first.');
        }

        $documentType->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Document type deleted successfully']);
        }

        return redirect()->route('snars.document-types.index')
            ->with('success', 'Document type deleted successfully');
    }

    /**
     * Toggle the active status of a document type.
     */
    public function toggleActive(string $id)
    {
        $documentType = SnarsDocumentType::findOrFail($id);
        $documentType->update([
            'is_active' => !$documentType->is_active,
            'updated_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $documentType,
                'message' => 'Document type status updated successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Document type status updated successfully');
    }
}
