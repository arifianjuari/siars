<?php

namespace App\Http\Controllers;

use App\Models\SnarsAssessmentElement;
use App\Models\SnarsStandard;
use App\Models\SnarsRequiredDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SnarsAssessmentElementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsAssessmentElement::with(['standard', 'standard.chapter', 'standard.chapter.group']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('assessment_guide', 'like', "%{$search}%")
                  ->orWhere('evidence_requirements', 'like', "%{$search}%");
            });
        }

        if ($request->has('standard_id')) {
            $query->where('standard_id', $request->input('standard_id'));
        }

        if ($request->has('chapter_id')) {
            $standardIds = SnarsStandard::where('chapter_id', $request->input('chapter_id'))->pluck('id');
            $query->whereIn('standard_id', $standardIds);
        }

        if ($request->has('group_id')) {
            $standardIds = SnarsStandard::whereHas('chapter', function ($q) use ($request) {
                $q->where('group_id', $request->input('group_id'));
            })->pluck('id');
            $query->whereIn('standard_id', $standardIds);
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
        $sortField = $request->input('sort_field', 'order');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $elements = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $elements->items(),
                'pagination' => [
                    'total' => $elements->total(),
                    'per_page' => $elements->perPage(),
                    'current_page' => $elements->currentPage(),
                    'last_page' => $elements->lastPage(),
                ],
            ]);
        }

        $standards = SnarsStandard::with(['chapter', 'chapter.group'])->active()->ordered()->get();
        return view('snars.assessment-elements.index', compact('elements', 'standards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $standards = SnarsStandard::with(['chapter', 'chapter.group'])->active()->ordered()->get();
        return view('snars.assessment-elements.create', compact('standards'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'standard_id' => 'required|string|exists:snars_standards,id',
            'code' => 'required|string|max:50|unique:snars_assessment_elements,code',
            'description' => 'required|string',
            'assessment_guide' => 'nullable|string',
            'evidence_requirements' => 'nullable|string',
            'scoring_method' => 'nullable|string|max:50',
            'weight' => 'nullable|integer|min:1',
            'order' => 'nullable|integer|min:1',
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
        
        if (!isset($data['weight'])) {
            $data['weight'] = 1;
        }
        
        if (!isset($data['order'])) {
            // Get the highest order for elements in the same standard and add 1
            $maxOrder = SnarsAssessmentElement::where('standard_id', $data['standard_id'])->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $element = SnarsAssessmentElement::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $element, 'message' => 'Assessment element created successfully'], 201);
        }

        return redirect()->route('snars.assessment-elements.index')
            ->with('success', 'Assessment element created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $element = SnarsAssessmentElement::with([
            'standard', 
            'standard.chapter', 
            'standard.chapter.group',
            'requiredDocuments',
            'documents',
            'assessmentScores'
        ])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $element]);
        }

        return view('snars.assessment-elements.show', compact('element'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $element = SnarsAssessmentElement::findOrFail($id);
        $standards = SnarsStandard::with(['chapter', 'chapter.group'])->active()->ordered()->get();
        return view('snars.assessment-elements.edit', compact('element', 'standards'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $element = SnarsAssessmentElement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'standard_id' => 'required|string|exists:snars_standards,id',
            'code' => ['required', 'string', 'max:50', Rule::unique('snars_assessment_elements', 'code')->ignore($element->id)],
            'description' => 'required|string',
            'assessment_guide' => 'nullable|string',
            'evidence_requirements' => 'nullable|string',
            'scoring_method' => 'nullable|string|max:50',
            'weight' => 'nullable|integer|min:1',
            'order' => 'nullable|integer|min:1',
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
            $data['is_active'] = $element->is_active;
        }

        if (!isset($data['weight'])) {
            $data['weight'] = $element->weight;
        }

        $element->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $element, 'message' => 'Assessment element updated successfully']);
        }

        return redirect()->route('snars.assessment-elements.index')
            ->with('success', 'Assessment element updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $element = SnarsAssessmentElement::findOrFail($id);

        // Check if the element has any required documents
        if ($element->requiredDocuments()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete assessment element because it has associated required documents. Remove the required documents first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete assessment element because it has associated required documents. Remove the required documents first.');
        }

        // Check if the element has any documents mapped
        if ($element->documents()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete assessment element because it has associated documents. Remove the document mappings first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete assessment element because it has associated documents. Remove the document mappings first.');
        }

        // Check if the element has any assessment scores
        if ($element->assessmentScores()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete assessment element because it has associated assessment scores. Remove the assessment scores first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete assessment element because it has associated assessment scores. Remove the assessment scores first.');
        }

        $element->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Assessment element deleted successfully']);
        }

        return redirect()->route('snars.assessment-elements.index')
            ->with('success', 'Assessment element deleted successfully');
    }

    /**
     * Update the order of multiple assessment elements.
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'elements' => 'required|array',
            'elements.*.id' => 'required|string|exists:snars_assessment_elements,id',
            'elements.*.order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $elements = $request->input('elements');

        foreach ($elements as $elementData) {
            $element = SnarsAssessmentElement::findOrFail($elementData['id']);
            $element->update([
                'order' => $elementData['order'],
                'updated_by' => Auth::id(),
            ]);
        }

        return response()->json(['message' => 'Assessment element order updated successfully']);
    }

    /**
     * Toggle the active status of an assessment element.
     */
    public function toggleActive(string $id)
    {
        $element = SnarsAssessmentElement::findOrFail($id);
        $element->update([
            'is_active' => !$element->is_active,
            'updated_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $element,
                'message' => 'Assessment element status updated successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Assessment element status updated successfully');
    }
}
