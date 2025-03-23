<?php

namespace App\Http\Controllers;

use App\Models\SnarsDashboardWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SnarsDashboardWidgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsDashboardWidget::with(['tenant', 'user', 'creator', 'updater']);

        // Apply filters
        if ($request->has('tenant_id')) {
            $query->forTenant($request->input('tenant_id'));
        }

        if ($request->has('user_id')) {
            $query->forUser($request->input('user_id'));
        } else {
            // Default to current user's widgets if no user_id specified
            $query->forUser(Auth::id());
        }

        if ($request->has('widget_type')) {
            $query->ofType($request->input('widget_type'));
        }

        if ($request->has('is_active')) {
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
            if ($isActive) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('widget_type', 'like', "%{$search}%")
                  ->orWhere('data_source', 'like', "%{$search}%");
            });
        }

        // Sort results
        $sortField = $request->input('sort_by', 'position');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $widgets = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $widgets,
                'message' => 'Dashboard widgets retrieved successfully'
            ]);
        }

        return view('snars.dashboard-widgets.index', compact('widgets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $widgetTypes = [
            'chart' => 'Chart',
            'counter' => 'Counter',
            'table' => 'Table',
            'status' => 'Status',
            'timeline' => 'Timeline',
            'custom' => 'Custom'
        ];

        $dataSources = [
            'compliance' => 'Compliance Data',
            'assessment' => 'Assessment Data',
            'findings' => 'Findings Data',
            'monitoring' => 'Monitoring Data',
            'documents' => 'Documents Data',
            'custom' => 'Custom Data Source'
        ];

        return view('snars.dashboard-widgets.create', compact('widgetTypes', 'dataSources'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tenant_id' => 'required|exists:tenants,id',
                'user_id' => 'nullable|exists:users,id',
                'title' => 'required|string|max:255',
                'widget_type' => 'required|string|max:50',
                'data_source' => 'required|string|max:100',
                'config' => 'required|json',
                'position' => 'required|integer|min:0',
                'size' => 'required|json',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Set default user to current user if not provided
            if (!isset($data['user_id'])) {
                $data['user_id'] = Auth::id();
            }
            
            // Set default active status if not provided
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }
            
            // Add user info
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $widget = SnarsDashboardWidget::create($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $widget,
                    'message' => 'Dashboard widget created successfully'
                ], 201);
            }

            return redirect()->route('snars.dashboard-widgets.index')
                ->with('success', 'Dashboard widget created successfully');
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
                    'message' => 'Failed to create dashboard widget',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create dashboard widget: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $widget = SnarsDashboardWidget::with(['tenant', 'user', 'creator', 'updater'])
                ->findOrFail($id);

            if (request()->expectsJson()) {
                return response()->json([
                    'data' => $widget,
                    'message' => 'Dashboard widget retrieved successfully'
                ]);
            }

            return view('snars.dashboard-widgets.show', compact('widget'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Dashboard widget not found',
                    'error' => $e->getMessage()
                ], 404);
            }

            return redirect()->route('snars.dashboard-widgets.index')
                ->with('error', 'Dashboard widget not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $widget = SnarsDashboardWidget::findOrFail($id);
            
            $widgetTypes = [
                'chart' => 'Chart',
                'counter' => 'Counter',
                'table' => 'Table',
                'status' => 'Status',
                'timeline' => 'Timeline',
                'custom' => 'Custom'
            ];

            $dataSources = [
                'compliance' => 'Compliance Data',
                'assessment' => 'Assessment Data',
                'findings' => 'Findings Data',
                'monitoring' => 'Monitoring Data',
                'documents' => 'Documents Data',
                'custom' => 'Custom Data Source'
            ];

            return view('snars.dashboard-widgets.edit', compact('widget', 'widgetTypes', 'dataSources'));
        } catch (\Exception $e) {
            return redirect()->route('snars.dashboard-widgets.index')
                ->with('error', 'Dashboard widget not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $widget = SnarsDashboardWidget::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tenant_id' => 'required|exists:tenants,id',
                'user_id' => 'nullable|exists:users,id',
                'title' => 'required|string|max:255',
                'widget_type' => 'required|string|max:50',
                'data_source' => 'required|string|max:100',
                'config' => 'required|json',
                'position' => 'required|integer|min:0',
                'size' => 'required|json',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Update user info
            $data['updated_by'] = Auth::id();

            $widget->update($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $widget,
                    'message' => 'Dashboard widget updated successfully'
                ]);
            }

            return redirect()->route('snars.dashboard-widgets.index')
                ->with('success', 'Dashboard widget updated successfully');
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
                    'message' => 'Failed to update dashboard widget',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update dashboard widget: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $widget = SnarsDashboardWidget::findOrFail($id);
            $widget->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Dashboard widget deleted successfully'
                ]);
            }

            return redirect()->route('snars.dashboard-widgets.index')
                ->with('success', 'Dashboard widget deleted successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete dashboard widget',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->route('snars.dashboard-widgets.index')
                ->with('error', 'Failed to delete dashboard widget: ' . $e->getMessage());
        }
    }

    /**
     * Update widget positions.
     */
    public function updatePositions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'widgets' => 'required|array',
                'widgets.*.id' => 'required|exists:snars_dashboard_widgets,id',
                'widgets.*.position' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $widgets = $request->input('widgets');
            
            foreach ($widgets as $widgetData) {
                $widget = SnarsDashboardWidget::findOrFail($widgetData['id']);
                $widget->update([
                    'position' => $widgetData['position'],
                    'updated_by' => Auth::id()
                ]);
            }

            return response()->json([
                'message' => 'Widget positions updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update widget positions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
