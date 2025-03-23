<?php

namespace App\Http\Controllers;

use App\Models\SnarsUpdate;
use App\Models\SnarsVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SnarsUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsUpdate::with(['version', 'creator', 'updater']);

        // Apply filters
        if ($request->has('version_id')) {
            $query->where('version_id', $request->input('version_id'));
        }

        if ($request->has('update_type')) {
            $query->ofType($request->input('update_type'));
        }

        if ($request->has('is_major')) {
            $isMajor = filter_var($request->input('is_major'), FILTER_VALIDATE_BOOLEAN);
            if ($isMajor) {
                $query->major();
            } else {
                $query->minor();
            }
        }

        if ($request->has('component')) {
            $query->affectingComponent($request->input('component'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('update_type', 'like', "%{$search}%");
            });
        }

        // Sort results
        $sortField = $request->input('sort_by', 'published_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $updates = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $updates,
                'message' => 'SNARS updates retrieved successfully'
            ]);
        }

        return view('snars.updates.index', compact('updates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $versions = SnarsVersion::all();
        $updateTypes = [
            'feature' => 'New Feature',
            'enhancement' => 'Enhancement',
            'bugfix' => 'Bug Fix',
            'security' => 'Security Update',
            'documentation' => 'Documentation',
            'other' => 'Other'
        ];

        $components = [
            'database' => 'Database',
            'ui' => 'User Interface',
            'api' => 'API',
            'reporting' => 'Reporting',
            'assessment' => 'Assessment',
            'monitoring' => 'Monitoring',
            'document' => 'Document Management',
            'notification' => 'Notification System',
            'other' => 'Other'
        ];

        return view('snars.updates.create', compact('versions', 'updateTypes', 'components'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'version_id' => 'required|exists:snars_versions,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:5000',
                'update_type' => 'required|string|max:50',
                'affected_components' => 'required|array',
                'affected_components.*' => 'string|max:50',
                'is_major' => 'boolean',
                'published_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Set default major status if not provided
            if (!isset($data['is_major'])) {
                $data['is_major'] = false;
            }
            
            // Set published_at to current time if not provided
            if (!isset($data['published_at'])) {
                $data['published_at'] = now();
            }
            
            // Add user info
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $update = SnarsUpdate::create($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $update,
                    'message' => 'SNARS update created successfully'
                ], 201);
            }

            return redirect()->route('snars.updates.show', $update->id)
                ->with('success', 'SNARS update created successfully');
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
                    'message' => 'Failed to create SNARS update',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create SNARS update: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $update = SnarsUpdate::with(['version', 'creator', 'updater'])
                ->findOrFail($id);

            if (request()->expectsJson()) {
                return response()->json([
                    'data' => $update,
                    'message' => 'SNARS update retrieved successfully'
                ]);
            }

            return view('snars.updates.show', compact('update'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'SNARS update not found',
                    'error' => $e->getMessage()
                ], 404);
            }

            return redirect()->route('snars.updates.index')
                ->with('error', 'SNARS update not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $update = SnarsUpdate::findOrFail($id);
            $versions = SnarsVersion::all();
            
            $updateTypes = [
                'feature' => 'New Feature',
                'enhancement' => 'Enhancement',
                'bugfix' => 'Bug Fix',
                'security' => 'Security Update',
                'documentation' => 'Documentation',
                'other' => 'Other'
            ];

            $components = [
                'database' => 'Database',
                'ui' => 'User Interface',
                'api' => 'API',
                'reporting' => 'Reporting',
                'assessment' => 'Assessment',
                'monitoring' => 'Monitoring',
                'document' => 'Document Management',
                'notification' => 'Notification System',
                'other' => 'Other'
            ];

            return view('snars.updates.edit', compact('update', 'versions', 'updateTypes', 'components'));
        } catch (\Exception $e) {
            return redirect()->route('snars.updates.index')
                ->with('error', 'SNARS update not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $update = SnarsUpdate::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'version_id' => 'required|exists:snars_versions,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:5000',
                'update_type' => 'required|string|max:50',
                'affected_components' => 'required|array',
                'affected_components.*' => 'string|max:50',
                'is_major' => 'boolean',
                'published_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Update user info
            $data['updated_by'] = Auth::id();

            $update->update($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $update,
                    'message' => 'SNARS update updated successfully'
                ]);
            }

            return redirect()->route('snars.updates.show', $update->id)
                ->with('success', 'SNARS update updated successfully');
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
                    'message' => 'Failed to update SNARS update',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update SNARS update: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $update = SnarsUpdate::findOrFail($id);
            $update->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'SNARS update deleted successfully'
                ]);
            }

            return redirect()->route('snars.updates.index')
                ->with('success', 'SNARS update deleted successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete SNARS update',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->route('snars.updates.index')
                ->with('error', 'Failed to delete SNARS update: ' . $e->getMessage());
        }
    }

    /**
     * Get updates for a specific version.
     */
    public function getVersionUpdates(string $versionId)
    {
        try {
            $updates = SnarsUpdate::with(['creator', 'updater'])
                ->where('version_id', $versionId)
                ->latestFirst()
                ->get();

            return response()->json([
                'data' => $updates,
                'message' => 'Version updates retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve version updates',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
