<?php

namespace App\Http\Controllers;

use App\Models\SnarsMonitoringSchedule;
use App\Models\SnarsAssessmentElement;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SnarsMonitoringScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsMonitoringSchedule::with(['element.standard.chapter.group', 'department', 'results']);

        // Apply filters
        if ($request->has('element_id')) {
            $query->forElement($request->input('element_id'));
        }

        if ($request->has('department_id')) {
            $query->inDepartment($request->input('department_id'));
        }

        if ($request->has('status')) {
            $query->withStatus($request->input('status'));
        }

        if ($request->has('frequency')) {
            $query->withFrequency($request->input('frequency'));
        }

        if ($request->has('filter_type')) {
            $filterType = $request->input('filter_type');
            if ($filterType === 'active') {
                $query->active();
            } elseif ($filterType === 'upcoming') {
                $query->upcoming();
            } elseif ($filterType === 'expired') {
                $query->expired();
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

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('element', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'start_date');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $schedules = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $schedules->items(),
                'pagination' => [
                    'total' => $schedules->total(),
                    'per_page' => $schedules->perPage(),
                    'current_page' => $schedules->currentPage(),
                    'last_page' => $schedules->lastPage(),
                ],
            ]);
        }

        // Get elements and departments for filter dropdowns
        $elements = SnarsAssessmentElement::with('standard.chapter.group')->get();
        $departments = Department::orderBy('name')->get();
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annually', 'annually'];
        $statuses = ['active', 'completed', 'cancelled', 'on_hold'];

        return view('snars.monitoring-schedules.index', compact('schedules', 'elements', 'departments', 'frequencies', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $elementId = $request->input('element_id');
        $departmentId = $request->input('department_id');
        $element = null;
        $department = null;
        
        if ($elementId) {
            $element = SnarsAssessmentElement::findOrFail($elementId);
        }
        
        if ($departmentId) {
            $department = Department::findOrFail($departmentId);
        }
        
        $elements = SnarsAssessmentElement::with('standard.chapter.group')->get();
        $departments = Department::orderBy('name')->get();
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annually', 'annually'];
        $statuses = ['active', 'completed', 'cancelled', 'on_hold'];
        
        return view('snars.monitoring-schedules.create', compact(
            'elements', 
            'departments', 
            'element', 
            'department', 
            'frequencies', 
            'statuses'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'element_id' => 'required|string|exists:snars_assessment_elements,id',
            'department_id' => 'required|string|exists:departments,id',
            'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,semi_annually,annually',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,completed,cancelled,on_hold',
            'notes' => 'nullable|string',
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

        $schedule = SnarsMonitoringSchedule::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $schedule, 'message' => 'Monitoring schedule created successfully'], 201);
        }

        return redirect()->route('snars.monitoring-schedules.index')
            ->with('success', 'Monitoring schedule created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = SnarsMonitoringSchedule::with([
            'element.standard.chapter.group', 
            'department', 
            'results.creator',
            'creator',
            'updater'
        ])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $schedule]);
        }

        return view('snars.monitoring-schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $schedule = SnarsMonitoringSchedule::findOrFail($id);
        $elements = SnarsAssessmentElement::with('standard.chapter.group')->get();
        $departments = Department::orderBy('name')->get();
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annually', 'annually'];
        $statuses = ['active', 'completed', 'cancelled', 'on_hold'];
        
        return view('snars.monitoring-schedules.edit', compact('schedule', 'elements', 'departments', 'frequencies', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $schedule = SnarsMonitoringSchedule::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'element_id' => 'required|string|exists:snars_assessment_elements,id',
            'department_id' => 'required|string|exists:departments,id',
            'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,semi_annually,annually',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,completed,cancelled,on_hold',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

        $schedule->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $schedule, 'message' => 'Monitoring schedule updated successfully']);
        }

        return redirect()->route('snars.monitoring-schedules.index')
            ->with('success', 'Monitoring schedule updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = SnarsMonitoringSchedule::findOrFail($id);
        
        // Check if there are any results associated with this schedule
        if ($schedule->results()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete monitoring schedule with associated results. Please delete the results first.'
                ], 422);
            }
            
            return redirect()->back()
                ->with('error', 'Cannot delete monitoring schedule with associated results. Please delete the results first.');
        }
        
        $schedule->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Monitoring schedule deleted successfully']);
        }

        return redirect()->route('snars.monitoring-schedules.index')
            ->with('success', 'Monitoring schedule deleted successfully');
    }

    /**
     * Get monitoring schedules for a specific element.
     */
    public function getByElement(string $elementId)
    {
        $schedules = SnarsMonitoringSchedule::with(['department'])
            ->forElement($elementId)
            ->orderBy('start_date', 'desc')
            ->get();
            
        return response()->json(['data' => $schedules]);
    }

    /**
     * Get monitoring schedules for a specific department.
     */
    public function getByDepartment(string $departmentId)
    {
        $schedules = SnarsMonitoringSchedule::with(['element.standard.chapter.group'])
            ->inDepartment($departmentId)
            ->orderBy('start_date', 'desc')
            ->get();
            
        return response()->json(['data' => $schedules]);
    }
}
