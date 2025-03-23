<?php

namespace App\Http\Controllers;

use App\Models\SnarsAssessment;
use App\Models\SnarsAssessmentPeriod;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SnarsAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsAssessment::with(['period', 'department', 'scores', 'findings']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('department', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('period', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('is_completed')) {
            $isCompleted = filter_var($request->input('is_completed'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_completed', $isCompleted);
        }

        if ($request->has('period_id')) {
            $query->where('period_id', $request->input('period_id'));
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // Date range filters
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('assessment_date', [$request->input('date_from'), $request->input('date_to')]);
        } elseif ($request->has('date_from')) {
            $query->whereDate('assessment_date', '>=', $request->input('date_from'));
        } elseif ($request->has('date_to')) {
            $query->whereDate('assessment_date', '<=', $request->input('date_to'));
        }

        // Score range filters
        if ($request->has('min_score') && $request->has('max_score')) {
            $query->whereBetween('overall_score', [$request->input('min_score'), $request->input('max_score')]);
        } elseif ($request->has('min_score')) {
            $query->where('overall_score', '>=', $request->input('min_score'));
        } elseif ($request->has('max_score')) {
            $query->where('overall_score', '<=', $request->input('max_score'));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'assessment_date');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $assessments = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $assessments->items(),
                'pagination' => [
                    'total' => $assessments->total(),
                    'per_page' => $assessments->perPage(),
                    'current_page' => $assessments->currentPage(),
                    'last_page' => $assessments->lastPage(),
                ],
            ]);
        }

        // Get periods and departments for filter dropdowns
        $periods = SnarsAssessmentPeriod::active()->orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();

        return view('snars.assessments.index', compact('assessments', 'periods', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $periods = SnarsAssessmentPeriod::active()->orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();
        
        return view('snars.assessments.create', compact('periods', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_id' => 'required|string|exists:snars_assessment_periods,id',
            'department_id' => 'required|string|exists:departments,id',
            'assessment_date' => 'required|date',
            'status' => 'required|string|in:planned,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'is_completed' => 'nullable|boolean',
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
        if (!isset($data['is_completed'])) {
            $data['is_completed'] = false;
        }

        // If assessment is marked as completed, set completed_at timestamp
        if ($data['is_completed']) {
            $data['completed_at'] = now();
        }

        $assessment = SnarsAssessment::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $assessment, 'message' => 'Assessment created successfully'], 201);
        }

        return redirect()->route('snars.assessments.index')
            ->with('success', 'Assessment created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assessment = SnarsAssessment::with(['period', 'department', 'scores.assessmentElement', 'findings'])
            ->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $assessment]);
        }

        return view('snars.assessments.show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $assessment = SnarsAssessment::findOrFail($id);
        $periods = SnarsAssessmentPeriod::active()->orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();
        
        return view('snars.assessments.edit', compact('assessment', 'periods', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $assessment = SnarsAssessment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'period_id' => 'required|string|exists:snars_assessment_periods,id',
            'department_id' => 'required|string|exists:departments,id',
            'assessment_date' => 'required|date',
            'status' => 'required|string|in:planned,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'is_completed' => 'nullable|boolean',
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
        if (!isset($data['is_completed'])) {
            $data['is_completed'] = $assessment->is_completed;
        }

        // If assessment is being marked as completed now, set completed_at timestamp
        if ($data['is_completed'] && !$assessment->is_completed) {
            $data['completed_at'] = now();
        } elseif (!$data['is_completed'] && $assessment->is_completed) {
            // If assessment is being unmarked as completed, clear completed_at
            $data['completed_at'] = null;
        }

        $assessment->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $assessment, 'message' => 'Assessment updated successfully']);
        }

        return redirect()->route('snars.assessments.index')
            ->with('success', 'Assessment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assessment = SnarsAssessment::findOrFail($id);

        // Check if the assessment has any scores or findings
        if ($assessment->scores()->count() > 0 || $assessment->findings()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete assessment because it has associated scores or findings. Remove them first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete assessment because it has associated scores or findings. Remove them first.');
        }

        $assessment->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Assessment deleted successfully']);
        }

        return redirect()->route('snars.assessments.index')
            ->with('success', 'Assessment deleted successfully');
    }

    /**
     * Mark an assessment as completed.
     */
    public function markAsCompleted(string $id)
    {
        $assessment = SnarsAssessment::findOrFail($id);
        
        // Calculate overall score based on assessment scores
        $overallScore = $this->calculateOverallScore($assessment);
        
        $assessment->update([
            'is_completed' => true,
            'completed_at' => now(),
            'overall_score' => $overallScore,
            'updated_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $assessment,
                'message' => 'Assessment marked as completed successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Assessment marked as completed successfully');
    }

    /**
     * Calculate the overall score for an assessment based on its assessment scores.
     */
    private function calculateOverallScore(SnarsAssessment $assessment)
    {
        $scores = $assessment->scores()->get();
        
        if ($scores->isEmpty()) {
            return 0;
        }
        
        $totalScore = $scores->sum('score');
        $maxPossibleScore = $scores->count() * 5; // Assuming max score per element is 5
        
        if ($maxPossibleScore === 0) {
            return 0;
        }
        
        return round(($totalScore / $maxPossibleScore) * 100, 2);
    }

    /**
     * Generate assessment report.
     */
    public function generateReport(string $id)
    {
        $assessment = SnarsAssessment::with([
            'period', 
            'department', 
            'scores.assessmentElement.standard.chapter.group', 
            'findings'
        ])->findOrFail($id);

        if (!$assessment->is_completed) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Cannot generate report for incomplete assessment'], 422);
            }
            return redirect()->back()->with('error', 'Cannot generate report for incomplete assessment');
        }

        // Group scores by chapter and standard for the report
        $scoresByGroup = [];
        foreach ($assessment->scores as $score) {
            $element = $score->assessmentElement;
            $standard = $element->standard;
            $chapter = $standard->chapter;
            $group = $chapter->group;
            
            if (!isset($scoresByGroup[$group->id])) {
                $scoresByGroup[$group->id] = [
                    'group' => $group,
                    'chapters' => []
                ];
            }
            
            if (!isset($scoresByGroup[$group->id]['chapters'][$chapter->id])) {
                $scoresByGroup[$group->id]['chapters'][$chapter->id] = [
                    'chapter' => $chapter,
                    'standards' => []
                ];
            }
            
            if (!isset($scoresByGroup[$group->id]['chapters'][$chapter->id]['standards'][$standard->id])) {
                $scoresByGroup[$group->id]['chapters'][$chapter->id]['standards'][$standard->id] = [
                    'standard' => $standard,
                    'elements' => []
                ];
            }
            
            $scoresByGroup[$group->id]['chapters'][$chapter->id]['standards'][$standard->id]['elements'][] = [
                'element' => $element,
                'score' => $score
            ];
        }

        if (request()->expectsJson()) {
            return response()->json([
                'assessment' => $assessment,
                'scores_by_group' => $scoresByGroup
            ]);
        }

        return view('snars.assessments.report', compact('assessment', 'scoresByGroup'));
    }
}
