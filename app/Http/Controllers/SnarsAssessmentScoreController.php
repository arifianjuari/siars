<?php

namespace App\Http\Controllers;

use App\Models\SnarsAssessment;
use App\Models\SnarsAssessmentElement;
use App\Models\SnarsAssessmentScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SnarsAssessmentScoreController extends Controller
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

        $query = SnarsAssessmentScore::with(['assessment', 'element.standard.chapter.group']);

        // Apply filters
        if ($request->has('assessment_id')) {
            $query->where('assessment_id', $request->input('assessment_id'));
        }

        if ($request->has('element_id')) {
            $query->where('element_id', $request->input('element_id'));
        }

        if ($request->has('min_score') && $request->has('max_score')) {
            $query->whereBetween('score', [$request->input('min_score'), $request->input('max_score')]);
        } elseif ($request->has('min_score')) {
            $query->where('score', '>=', $request->input('min_score'));
        } elseif ($request->has('max_score')) {
            $query->where('score', '<=', $request->input('max_score'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhere('evidence', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $scores = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $scores->items(),
                'pagination' => [
                    'total' => $scores->total(),
                    'per_page' => $scores->perPage(),
                    'current_page' => $scores->currentPage(),
                    'last_page' => $scores->lastPage(),
                ],
            ]);
        }

        // Get assessments and elements for filter dropdowns
        $assessments = SnarsAssessment::orderBy('assessment_date', 'desc')->get();
        $elements = SnarsAssessmentElement::with('standard.chapter.group')->get();

        return view('snars.assessment-scores.index', compact('scores', 'assessments', 'elements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $assessmentId = $request->input('assessment_id');
        $assessment = null;
        
        if ($assessmentId) {
            $assessment = SnarsAssessment::findOrFail($assessmentId);
        }
        
        $assessments = SnarsAssessment::orderBy('assessment_date', 'desc')->get();
        $elements = SnarsAssessmentElement::with('standard.chapter.group')->get();
        
        return view('snars.assessment-scores.create', compact('assessments', 'elements', 'assessment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|string|exists:snars_assessments,id',
            'element_id' => 'required|string|exists:snars_assessment_elements,id',
            'score' => 'required|numeric|min:0|max:5',
            'notes' => 'nullable|string',
            'evidence' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if a score already exists for this assessment and element
        $existingScore = SnarsAssessmentScore::where('assessment_id', $request->input('assessment_id'))
            ->where('element_id', $request->input('element_id'))
            ->first();

        if ($existingScore) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'A score already exists for this assessment element. Please update the existing score instead.'
                ], 422);
            }
            return redirect()->back()
                ->with('error', 'A score already exists for this assessment element. Please update the existing score instead.')
                ->withInput();
        }

        $data = $validator->validated();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $score = SnarsAssessmentScore::create($data);

        // Update the overall score for the assessment
        $this->updateAssessmentOverallScore($score->assessment_id);

        if ($request->expectsJson()) {
            return response()->json(['data' => $score, 'message' => 'Assessment score created successfully'], 201);
        }

        return redirect()->route('snars.assessment-scores.index', ['assessment_id' => $score->assessment_id])
            ->with('success', 'Assessment score created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $score = SnarsAssessmentScore::with(['assessment', 'element.standard.chapter.group'])
            ->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $score]);
        }

        return view('snars.assessment-scores.show', compact('score'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $score = SnarsAssessmentScore::findOrFail($id);
        $assessments = SnarsAssessment::orderBy('assessment_date', 'desc')->get();
        $elements = SnarsAssessmentElement::with('standard.chapter.group')->get();
        
        return view('snars.assessment-scores.edit', compact('score', 'assessments', 'elements'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $score = SnarsAssessmentScore::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|string|exists:snars_assessments,id',
            'element_id' => 'required|string|exists:snars_assessment_elements,id',
            'score' => 'required|numeric|min:0|max:5',
            'notes' => 'nullable|string',
            'evidence' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // If changing the assessment or element, check for existing scores
        if ($score->assessment_id != $request->input('assessment_id') || 
            $score->element_id != $request->input('element_id')) {
            
            $existingScore = SnarsAssessmentScore::where('assessment_id', $request->input('assessment_id'))
                ->where('element_id', $request->input('element_id'))
                ->where('id', '!=', $id)
                ->first();

            if ($existingScore) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'A score already exists for this assessment element. Please update the existing score instead.'
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', 'A score already exists for this assessment element. Please update the existing score instead.')
                    ->withInput();
            }
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

        // Store the old assessment ID to update its overall score if it changed
        $oldAssessmentId = $score->assessment_id;
        
        $score->update($data);

        // Update the overall score for affected assessments
        $this->updateAssessmentOverallScore($score->assessment_id);
        if ($oldAssessmentId !== $score->assessment_id) {
            $this->updateAssessmentOverallScore($oldAssessmentId);
        }

        if ($request->expectsJson()) {
            return response()->json(['data' => $score, 'message' => 'Assessment score updated successfully']);
        }

        return redirect()->route('snars.assessment-scores.index', ['assessment_id' => $score->assessment_id])
            ->with('success', 'Assessment score updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $score = SnarsAssessmentScore::findOrFail($id);
        $assessmentId = $score->assessment_id;
        
        $score->delete();

        // Update the overall score for the assessment
        $this->updateAssessmentOverallScore($assessmentId);

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Assessment score deleted successfully']);
        }

        return redirect()->route('snars.assessment-scores.index', ['assessment_id' => $assessmentId])
            ->with('success', 'Assessment score deleted successfully');
    }

    /**
     * Bulk create or update scores for an assessment.
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|string|exists:snars_assessments,id',
            'scores' => 'required|array',
            'scores.*.element_id' => 'required|string|exists:snars_assessment_elements,id',
            'scores.*.score' => 'required|numeric|min:0|max:5',
            'scores.*.notes' => 'nullable|string',
            'scores.*.evidence' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $assessmentId = $request->input('assessment_id');
        $scores = $request->input('scores');
        $userId = Auth::id();

        foreach ($scores as $scoreData) {
            $existingScore = SnarsAssessmentScore::where('assessment_id', $assessmentId)
                ->where('element_id', $scoreData['element_id'])
                ->first();

            if ($existingScore) {
                // Update existing score
                $existingScore->update([
                    'score' => $scoreData['score'],
                    'notes' => $scoreData['notes'] ?? $existingScore->notes,
                    'evidence' => $scoreData['evidence'] ?? $existingScore->evidence,
                    'updated_by' => $userId,
                ]);
            } else {
                // Create new score
                SnarsAssessmentScore::create([
                    'assessment_id' => $assessmentId,
                    'element_id' => $scoreData['element_id'],
                    'score' => $scoreData['score'],
                    'notes' => $scoreData['notes'] ?? null,
                    'evidence' => $scoreData['evidence'] ?? null,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // Update the overall score for the assessment
        $this->updateAssessmentOverallScore($assessmentId);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Assessment scores updated successfully']);
        }

        return redirect()->route('snars.assessment-scores.index', ['assessment_id' => $assessmentId])
            ->with('success', 'Assessment scores updated successfully');
    }

    /**
     * Update the overall score for an assessment based on its assessment scores.
     */
    private function updateAssessmentOverallScore(string $assessmentId)
    {
        $assessment = SnarsAssessment::findOrFail($assessmentId);
        $scores = $assessment->scores()->get();
        
        if ($scores->isEmpty()) {
            $assessment->update(['overall_score' => 0]);
            return;
        }
        
        $totalScore = $scores->sum('score');
        $maxPossibleScore = $scores->count() * 5; // Assuming max score per element is 5
        
        if ($maxPossibleScore === 0) {
            $assessment->update(['overall_score' => 0]);
            return;
        }
        
        $overallScore = round(($totalScore / $maxPossibleScore) * 100, 2);
        $assessment->update(['overall_score' => $overallScore]);
    }
}
