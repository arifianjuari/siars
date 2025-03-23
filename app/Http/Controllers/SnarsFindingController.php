<?php

namespace App\Http\Controllers;

use App\Models\SnarsFinding;
use App\Models\SnarsAssessment;
use App\Models\SnarsAssessmentElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SnarsFindingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate assessment_id if provided
        if ($request->has('assessment_id')) {
            $validator = Validator::make($request->all(), [
                'assessment_id' => 'required|string|exists:snars_assessments,id',
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                return redirect()->back()->withErrors($validator);
            }
        }

        $query = SnarsFinding::with(['assessment', 'element.standard.chapter.group']);

        // Apply filters
        if ($request->has('assessment_id')) {
            $query->where('assessment_id', $request->input('assessment_id'));
        }

        if ($request->has('element_id')) {
            $query->where('element_id', $request->input('element_id'));
        }

        if ($request->has('finding_type')) {
            $query->where('finding_type', $request->input('finding_type'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('is_resolved')) {
            $isResolved = filter_var($request->input('is_resolved'), FILTER_VALIDATE_BOOLEAN);
            if ($isResolved) {
                $query->resolved();
            } else {
                $query->unresolved();
            }
        }

        if ($request->has('is_overdue')) {
            $isOverdue = filter_var($request->input('is_overdue'), FILTER_VALIDATE_BOOLEAN);
            if ($isOverdue) {
                $query->overdue();
            }
        }

        // Date range filters
        if ($request->has('due_from') && $request->has('due_to')) {
            $query->whereBetween('due_date', [$request->input('due_from'), $request->input('due_to')]);
        } elseif ($request->has('due_from')) {
            $query->whereDate('due_date', '>=', $request->input('due_from'));
        } elseif ($request->has('due_to')) {
            $query->whereDate('due_date', '<=', $request->input('due_to'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('recommendation', 'like', "%{$search}%")
                  ->orWhere('resolution_notes', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $findings = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $findings->items(),
                'pagination' => [
                    'total' => $findings->total(),
                    'per_page' => $findings->perPage(),
                    'current_page' => $findings->currentPage(),
                    'last_page' => $findings->lastPage(),
                ],
            ]);
        }

        // Get assessments and elements for filter dropdowns
        $assessments = SnarsAssessment::orderBy('assessment_date', 'desc')->get();
        $elements = SnarsAssessmentElement::with('standard.chapter.group')->get();
        $findingTypes = ['non_conformity', 'observation', 'opportunity_for_improvement'];
        $statuses = ['open', 'in_progress', 'pending_verification', 'closed'];

        return view('snars.findings.index', compact('findings', 'assessments', 'elements', 'findingTypes', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $assessmentId = $request->input('assessment_id');
        $elementId = $request->input('element_id');
        $assessment = null;
        $element = null;
        
        if ($assessmentId) {
            $assessment = SnarsAssessment::findOrFail($assessmentId);
        }
        
        if ($elementId) {
            $element = SnarsAssessmentElement::findOrFail($elementId);
        }
        
        $assessments = SnarsAssessment::orderBy('assessment_date', 'desc')->get();
        $elements = SnarsAssessmentElement::with('standard.chapter.group')->get();
        $findingTypes = ['non_conformity', 'observation', 'opportunity_for_improvement'];
        $statuses = ['open', 'in_progress', 'pending_verification', 'closed'];
        
        return view('snars.findings.create', compact('assessments', 'elements', 'assessment', 'element', 'findingTypes', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|string|exists:snars_assessments,id',
            'element_id' => 'required|string|exists:snars_assessment_elements,id',
            'finding_type' => 'required|string|in:non_conformity,observation,opportunity_for_improvement',
            'description' => 'required|string',
            'recommendation' => 'required|string',
            'status' => 'required|string|in:open,in_progress,pending_verification,closed',
            'due_date' => 'required|date',
            'resolution_notes' => 'nullable|string',
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

        // If status is closed, set resolved_at timestamp
        if ($data['status'] === 'closed') {
            $data['resolved_at'] = now();
        }

        $finding = SnarsFinding::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $finding, 'message' => 'Finding created successfully'], 201);
        }

        return redirect()->route('snars.findings.index', ['assessment_id' => $finding->assessment_id])
            ->with('success', 'Finding created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $finding = SnarsFinding::with(['assessment', 'element.standard.chapter.group'])
            ->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $finding]);
        }

        return view('snars.findings.show', compact('finding'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $finding = SnarsFinding::findOrFail($id);
        $assessments = SnarsAssessment::orderBy('assessment_date', 'desc')->get();
        $elements = SnarsAssessmentElement::with('standard.chapter.group')->get();
        $findingTypes = ['non_conformity', 'observation', 'opportunity_for_improvement'];
        $statuses = ['open', 'in_progress', 'pending_verification', 'closed'];
        
        return view('snars.findings.edit', compact('finding', 'assessments', 'elements', 'findingTypes', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $finding = SnarsFinding::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|string|exists:snars_assessments,id',
            'element_id' => 'required|string|exists:snars_assessment_elements,id',
            'finding_type' => 'required|string|in:non_conformity,observation,opportunity_for_improvement',
            'description' => 'required|string',
            'recommendation' => 'required|string',
            'status' => 'required|string|in:open,in_progress,pending_verification,closed',
            'due_date' => 'required|date',
            'resolution_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

        // Handle resolved_at timestamp based on status changes
        $wasResolved = $finding->resolved_at !== null;
        $isNowClosed = $data['status'] === 'closed';

        if (!$wasResolved && $isNowClosed) {
            // Finding is being closed now
            $data['resolved_at'] = now();
        } elseif ($wasResolved && !$isNowClosed) {
            // Finding was closed but is being reopened
            $data['resolved_at'] = null;
        }

        $finding->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $finding, 'message' => 'Finding updated successfully']);
        }

        return redirect()->route('snars.findings.index', ['assessment_id' => $finding->assessment_id])
            ->with('success', 'Finding updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $finding = SnarsFinding::findOrFail($id);
        $assessmentId = $finding->assessment_id;
        
        $finding->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Finding deleted successfully']);
        }

        return redirect()->route('snars.findings.index', ['assessment_id' => $assessmentId])
            ->with('success', 'Finding deleted successfully');
    }

    /**
     * Mark a finding as resolved.
     */
    public function resolve(Request $request, string $id)
    {
        $finding = SnarsFinding::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'resolution_notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $finding->update([
            'status' => 'closed',
            'resolved_at' => now(),
            'resolution_notes' => $request->input('resolution_notes'),
            'updated_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $finding,
                'message' => 'Finding resolved successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Finding resolved successfully');
    }

    /**
     * Reopen a previously resolved finding.
     */
    public function reopen(string $id)
    {
        $finding = SnarsFinding::findOrFail($id);

        $finding->update([
            'status' => 'open',
            'resolved_at' => null,
            'updated_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $finding,
                'message' => 'Finding reopened successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Finding reopened successfully');
    }
}
