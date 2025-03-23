<?php

namespace App\Http\Controllers;

use App\Models\SnarsMonitoringResult;
use App\Models\SnarsMonitoringSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SnarsMonitoringResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate schedule_id if provided
        if ($request->has('schedule_id')) {
            $validator = Validator::make($request->all(), [
                'schedule_id' => 'required|string|exists:snars_monitoring_schedules,id',
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                return redirect()->back()->withErrors($validator);
            }
        }

        $query = SnarsMonitoringResult::with(['schedule.element.standard.chapter.group', 'schedule.department', 'creator']);

        // Apply filters
        if ($request->has('schedule_id')) {
            $query->forSchedule($request->input('schedule_id'));
        }

        if ($request->has('status')) {
            $query->withStatus($request->input('status'));
        }

        // Date range filters
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->betweenDates($request->input('from_date'), $request->input('to_date'));
        } elseif ($request->has('from_date')) {
            $query->whereDate('monitoring_date', '>=', $request->input('from_date'));
        } elseif ($request->has('to_date')) {
            $query->whereDate('monitoring_date', '<=', $request->input('to_date'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhere('result', 'like', "%{$search}%")
                  ->orWhereHas('schedule.element', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('schedule.department', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'monitoring_date');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $results = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $results->items(),
                'pagination' => [
                    'total' => $results->total(),
                    'per_page' => $results->perPage(),
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                ],
            ]);
        }

        // Get schedules for filter dropdown
        $schedules = SnarsMonitoringSchedule::with(['element.standard.chapter.group', 'department'])
            ->orderBy('start_date', 'desc')
            ->get();
        $statuses = ['compliant', 'non_compliant', 'partially_compliant', 'not_applicable'];

        return view('snars.monitoring-results.index', compact('results', 'schedules', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $scheduleId = $request->input('schedule_id');
        $schedule = null;
        
        if ($scheduleId) {
            $schedule = SnarsMonitoringSchedule::with(['element.standard.chapter.group', 'department'])
                ->findOrFail($scheduleId);
        }
        
        $schedules = SnarsMonitoringSchedule::with(['element.standard.chapter.group', 'department'])
            ->orderBy('start_date', 'desc')
            ->get();
        $statuses = ['compliant', 'non_compliant', 'partially_compliant', 'not_applicable'];
        
        return view('snars.monitoring-results.create', compact('schedules', 'schedule', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|string|exists:snars_monitoring_schedules,id',
            'monitoring_date' => 'required|date',
            'result' => 'required|string|in:compliant,non_compliant,partially_compliant,not_applicable',
            'notes' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'status' => 'required|string|in:draft,submitted,approved,rejected',
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

        // Handle file upload if evidence is provided
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $fileName = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('monitoring-evidences', $fileName, 'public');
            $data['evidence'] = $filePath;
        }

        $result = SnarsMonitoringResult::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $result, 'message' => 'Monitoring result created successfully'], 201);
        }

        return redirect()->route('snars.monitoring-results.index', ['schedule_id' => $result->schedule_id])
            ->with('success', 'Monitoring result created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $result = SnarsMonitoringResult::with([
            'schedule.element.standard.chapter.group', 
            'schedule.department', 
            'creator',
            'updater'
        ])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $result]);
        }

        return view('snars.monitoring-results.show', compact('result'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $result = SnarsMonitoringResult::with(['schedule.element.standard.chapter.group', 'schedule.department'])
            ->findOrFail($id);
        $schedules = SnarsMonitoringSchedule::with(['element.standard.chapter.group', 'department'])
            ->orderBy('start_date', 'desc')
            ->get();
        $statuses = ['compliant', 'non_compliant', 'partially_compliant', 'not_applicable'];
        
        return view('snars.monitoring-results.edit', compact('result', 'schedules', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $result = SnarsMonitoringResult::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|string|exists:snars_monitoring_schedules,id',
            'monitoring_date' => 'required|date',
            'result' => 'required|string|in:compliant,non_compliant,partially_compliant,not_applicable',
            'notes' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'status' => 'required|string|in:draft,submitted,approved,rejected',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

        // Handle file upload if evidence is provided
        if ($request->hasFile('evidence')) {
            // Delete old file if it exists
            if ($result->evidence) {
                Storage::disk('public')->delete($result->evidence);
            }
            
            $file = $request->file('evidence');
            $fileName = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('monitoring-evidences', $fileName, 'public');
            $data['evidence'] = $filePath;
        }

        $result->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $result, 'message' => 'Monitoring result updated successfully']);
        }

        return redirect()->route('snars.monitoring-results.index', ['schedule_id' => $result->schedule_id])
            ->with('success', 'Monitoring result updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = SnarsMonitoringResult::findOrFail($id);
        $scheduleId = $result->schedule_id;
        
        // Delete evidence file if it exists
        if ($result->evidence) {
            Storage::disk('public')->delete($result->evidence);
        }
        
        $result->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Monitoring result deleted successfully']);
        }

        return redirect()->route('snars.monitoring-results.index', ['schedule_id' => $scheduleId])
            ->with('success', 'Monitoring result deleted successfully');
    }

    /**
     * Download evidence file.
     */
    public function downloadEvidence(string $id)
    {
        $result = SnarsMonitoringResult::findOrFail($id);
        
        if (!$result->evidence) {
            return redirect()->back()->with('error', 'No evidence file found for this monitoring result.');
        }
        
        $path = Storage::disk('public')->path($result->evidence);
        
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Evidence file not found on server.');
        }
        
        return response()->download($path);
    }

    /**
     * Get monitoring results for a specific schedule.
     */
    public function getBySchedule(string $scheduleId)
    {
        $results = SnarsMonitoringResult::with(['creator'])
            ->forSchedule($scheduleId)
            ->orderBy('monitoring_date', 'desc')
            ->get();
            
        return response()->json(['data' => $results]);
    }

    /**
     * Change the status of a monitoring result.
     */
    public function changeStatus(Request $request, string $id)
    {
        $result = SnarsMonitoringResult::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:draft,submitted,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $result->update([
            'status' => $request->input('status'),
            'notes' => $request->has('notes') ? $result->notes . "\n\n[Status Change: {$request->input('status')}] " . $request->input('notes') : $result->notes,
            'updated_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['data' => $result, 'message' => 'Monitoring result status updated successfully']);
        }

        return redirect()->back()
            ->with('success', 'Monitoring result status updated successfully');
    }
}
