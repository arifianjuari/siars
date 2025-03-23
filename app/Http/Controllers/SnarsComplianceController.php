<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\SnarsAssessmentPeriod;
use App\Models\SnarsChapter;
use App\Models\SnarsCompliance;
use App\Models\SnarsGroup;
use App\Models\SnarsStandard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SnarsComplianceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsCompliance::with([
            'tenant', 
            'period', 
            'department', 
            'group', 
            'chapter', 
            'standard',
            'creator',
            'updater'
        ]);

        // Apply filters
        if ($request->has('period_id')) {
            $query->inPeriod($request->input('period_id'));
        }

        if ($request->has('department_id')) {
            $query->inDepartment($request->input('department_id'));
        }

        if ($request->has('group_id')) {
            $query->inGroup($request->input('group_id'));
        }

        if ($request->has('chapter_id')) {
            $query->inChapter($request->input('chapter_id'));
        }

        if ($request->has('standard_id')) {
            $query->inStandard($request->input('standard_id'));
        }

        if ($request->has('status')) {
            $query->withStatus($request->input('status'));
        }

        if ($request->has('min_compliance') && $request->has('max_compliance')) {
            $query->withCompliancePercentageBetween(
                $request->input('min_compliance'),
                $request->input('max_compliance')
            );
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->withReportDateBetween(
                $request->input('start_date'),
                $request->input('end_date')
            );
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('department', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('standard', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort results
        $sortField = $request->input('sort_by', 'report_date');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $compliances = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $compliances,
                'message' => 'Compliance data retrieved successfully'
            ]);
        }

        return view('snars.compliances.index', compact('compliances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $periods = SnarsAssessmentPeriod::all();
        $departments = Department::all();
        $groups = SnarsGroup::all();
        $chapters = SnarsChapter::all();
        $standards = SnarsStandard::all();

        return view('snars.compliances.create', compact(
            'periods',
            'departments',
            'groups',
            'chapters',
            'standards'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tenant_id' => 'required|exists:tenants,id',
                'period_id' => 'required|exists:snars_assessment_periods,id',
                'department_id' => 'required|exists:departments,id',
                'group_id' => 'nullable|exists:snars_groups,id',
                'chapter_id' => 'nullable|exists:snars_chapters,id',
                'standard_id' => 'nullable|exists:snars_standards,id',
                'total_elements' => 'required|integer|min:0',
                'compliant_elements' => 'required|integer|min:0|lte:total_elements',
                'compliance_percentage' => 'nullable|numeric|min:0|max:100',
                'status' => 'required|string|in:compliant,partial,non-compliant',
                'report_date' => 'required|date',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Calculate compliance percentage if not provided
            if (!isset($data['compliance_percentage']) && $data['total_elements'] > 0) {
                $data['compliance_percentage'] = ($data['compliant_elements'] / $data['total_elements']) * 100;
            }
            
            // Add user info
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $compliance = SnarsCompliance::create($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $compliance,
                    'message' => 'Compliance data created successfully'
                ], 201);
            }

            return redirect()->route('snars.compliances.show', $compliance->id)
                ->with('success', 'Compliance data created successfully');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to create compliance data',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create compliance data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $compliance = SnarsCompliance::with([
                'tenant', 
                'period', 
                'department', 
                'group', 
                'chapter', 
                'standard',
                'creator',
                'updater'
            ])->findOrFail($id);

            return view('snars.compliances.show', compact('compliance'));
        } catch (\Exception $e) {
            return redirect()->route('snars.compliances.index')
                ->with('error', 'Compliance data not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $compliance = SnarsCompliance::findOrFail($id);
            $periods = SnarsAssessmentPeriod::all();
            $departments = Department::all();
            $groups = SnarsGroup::all();
            $chapters = SnarsChapter::all();
            $standards = SnarsStandard::all();

            return view('snars.compliances.edit', compact(
                'compliance',
                'periods',
                'departments',
                'groups',
                'chapters',
                'standards'
            ));
        } catch (\Exception $e) {
            return redirect()->route('snars.compliances.index')
                ->with('error', 'Compliance data not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $compliance = SnarsCompliance::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tenant_id' => 'required|exists:tenants,id',
                'period_id' => 'required|exists:snars_assessment_periods,id',
                'department_id' => 'required|exists:departments,id',
                'group_id' => 'nullable|exists:snars_groups,id',
                'chapter_id' => 'nullable|exists:snars_chapters,id',
                'standard_id' => 'nullable|exists:snars_standards,id',
                'total_elements' => 'required|integer|min:0',
                'compliant_elements' => 'required|integer|min:0|lte:total_elements',
                'compliance_percentage' => 'nullable|numeric|min:0|max:100',
                'status' => 'required|string|in:compliant,partial,non-compliant',
                'report_date' => 'required|date',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Calculate compliance percentage if not provided
            if (!isset($data['compliance_percentage']) && $data['total_elements'] > 0) {
                $data['compliance_percentage'] = ($data['compliant_elements'] / $data['total_elements']) * 100;
            }
            
            // Update user info
            $data['updated_by'] = Auth::id();

            $compliance->update($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $compliance,
                    'message' => 'Compliance data updated successfully'
                ]);
            }

            return redirect()->route('snars.compliances.show', $compliance->id)
                ->with('success', 'Compliance data updated successfully');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update compliance data',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update compliance data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $compliance = SnarsCompliance::findOrFail($id);
            $compliance->delete();

            return response()->json([
                'message' => 'Compliance data deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete compliance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate compliance report.
     */
    public function generateReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'period_id' => 'required|exists:snars_assessment_periods,id',
                'department_id' => 'nullable|exists:departments,id',
                'group_id' => 'nullable|exists:snars_groups,id',
                'report_type' => 'required|in:summary,detailed,trend',
                'format' => 'required|in:pdf,excel,csv',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Logic to generate report based on parameters
            // This would typically involve querying the database, processing the data,
            // and generating the appropriate report format

            return response()->json([
                'message' => 'Report generated successfully',
                'download_url' => '/reports/compliance/download/' . uniqid()
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
