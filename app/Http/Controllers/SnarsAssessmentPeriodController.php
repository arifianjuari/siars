<?php

namespace App\Http\Controllers;

use App\Models\SnarsAssessmentPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SnarsAssessmentPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsAssessmentPeriod::with(['assessments']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('is_active')) {
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }

        if ($request->has('period')) {
            $period = $request->input('period');
            if ($period === 'current') {
                $query->current();
            } elseif ($period === 'upcoming') {
                $query->upcoming();
            } elseif ($period === 'past') {
                $query->past();
            }
        }

        // Date range filters
        if ($request->has('start_from') && $request->has('start_to')) {
            $query->whereBetween('start_date', [$request->input('start_from'), $request->input('start_to')]);
        } elseif ($request->has('start_from')) {
            $query->whereDate('start_date', '>=', $request->input('start_from'));
        } elseif ($request->has('start_to')) {
            $query->whereDate('start_date', '<=', $request->input('start_to'));
        }

        if ($request->has('end_from') && $request->has('end_to')) {
            $query->whereBetween('end_date', [$request->input('end_from'), $request->input('end_to')]);
        } elseif ($request->has('end_from')) {
            $query->whereDate('end_date', '>=', $request->input('end_from'));
        } elseif ($request->has('end_to')) {
            $query->whereDate('end_date', '<=', $request->input('end_to'));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'start_date');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $assessmentPeriods = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $assessmentPeriods->items(),
                'pagination' => [
                    'total' => $assessmentPeriods->total(),
                    'per_page' => $assessmentPeriods->perPage(),
                    'current_page' => $assessmentPeriods->currentPage(),
                    'last_page' => $assessmentPeriods->lastPage(),
                ],
            ]);
        }

        return view('snars.assessment-periods.index', compact('assessmentPeriods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('snars.assessment-periods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:planned,in_progress,completed,cancelled',
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
        $data['tenant_id'] = session('tenant_id'); // Assuming tenant_id is stored in session
        
        // Set default values if not provided
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        $assessmentPeriod = SnarsAssessmentPeriod::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $assessmentPeriod, 'message' => 'Assessment period created successfully'], 201);
        }

        return redirect()->route('snars.assessment-periods.index')
            ->with('success', 'Assessment period created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assessmentPeriod = SnarsAssessmentPeriod::with(['assessments'])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $assessmentPeriod]);
        }

        return view('snars.assessment-periods.show', compact('assessmentPeriod'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $assessmentPeriod = SnarsAssessmentPeriod::findOrFail($id);
        return view('snars.assessment-periods.edit', compact('assessmentPeriod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $assessmentPeriod = SnarsAssessmentPeriod::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:planned,in_progress,completed,cancelled',
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
            $data['is_active'] = $assessmentPeriod->is_active;
        }

        $assessmentPeriod->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $assessmentPeriod, 'message' => 'Assessment period updated successfully']);
        }

        return redirect()->route('snars.assessment-periods.index')
            ->with('success', 'Assessment period updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assessmentPeriod = SnarsAssessmentPeriod::findOrFail($id);

        // Check if the assessment period has any assessments
        if ($assessmentPeriod->assessments()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete assessment period because it has associated assessments. Remove the assessments first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete assessment period because it has associated assessments. Remove the assessments first.');
        }

        $assessmentPeriod->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Assessment period deleted successfully']);
        }

        return redirect()->route('snars.assessment-periods.index')
            ->with('success', 'Assessment period deleted successfully');
    }

    /**
     * Toggle the active status of an assessment period.
     */
    public function toggleActive(string $id)
    {
        $assessmentPeriod = SnarsAssessmentPeriod::findOrFail($id);
        $assessmentPeriod->update([
            'is_active' => !$assessmentPeriod->is_active,
            'updated_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $assessmentPeriod,
                'message' => 'Assessment period status updated successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Assessment period status updated successfully');
    }
}
