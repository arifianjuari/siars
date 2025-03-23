<?php

namespace App\Http\Controllers;

use App\Models\SnarsVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SnarsVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsVersion::with(['updates', 'creator', 'updater']);

        // Apply filters
        if ($request->has('is_active')) {
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
            if ($isActive) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->releasedBetween(
                $request->input('start_date'),
                $request->input('end_date')
            );
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('version_number', 'like', "%{$search}%");
            });
        }

        // Sort results
        $sortField = $request->input('sort_by', 'version_number');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $versions = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $versions,
                'message' => 'SNARS versions retrieved successfully'
            ]);
        }

        return view('snars.versions.index', compact('versions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('snars.versions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'version_number' => 'required|numeric|min:0',
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'release_date' => 'required|date',
                'is_active' => 'boolean',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Set default active status if not provided
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }
            
            // Add user info
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $version = SnarsVersion::create($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $version,
                    'message' => 'SNARS version created successfully'
                ], 201);
            }

            return redirect()->route('snars.versions.show', $version->id)
                ->with('success', 'SNARS version created successfully');
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
                    'message' => 'Failed to create SNARS version',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create SNARS version: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $version = SnarsVersion::with(['updates', 'creator', 'updater'])
                ->findOrFail($id);

            if (request()->expectsJson()) {
                return response()->json([
                    'data' => $version,
                    'message' => 'SNARS version retrieved successfully'
                ]);
            }

            return view('snars.versions.show', compact('version'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'SNARS version not found',
                    'error' => $e->getMessage()
                ], 404);
            }

            return redirect()->route('snars.versions.index')
                ->with('error', 'SNARS version not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $version = SnarsVersion::findOrFail($id);
            return view('snars.versions.edit', compact('version'));
        } catch (\Exception $e) {
            return redirect()->route('snars.versions.index')
                ->with('error', 'SNARS version not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $version = SnarsVersion::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'version_number' => 'required|numeric|min:0',
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'release_date' => 'required|date',
                'is_active' => 'boolean',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            
            // Update user info
            $data['updated_by'] = Auth::id();

            $version->update($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $version,
                    'message' => 'SNARS version updated successfully'
                ]);
            }

            return redirect()->route('snars.versions.show', $version->id)
                ->with('success', 'SNARS version updated successfully');
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
                    'message' => 'Failed to update SNARS version',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update SNARS version: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $version = SnarsVersion::findOrFail($id);
            $version->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'SNARS version deleted successfully'
                ]);
            }

            return redirect()->route('snars.versions.index')
                ->with('success', 'SNARS version deleted successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete SNARS version',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->route('snars.versions.index')
                ->with('error', 'Failed to delete SNARS version: ' . $e->getMessage());
        }
    }

    /**
     * Get the latest active version.
     */
    public function getLatest()
    {
        try {
            $version = SnarsVersion::getLatestActive();
            
            if (!$version) {
                return response()->json([
                    'message' => 'No active SNARS version found'
                ], 404);
            }

            return response()->json([
                'data' => $version,
                'message' => 'Latest SNARS version retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve latest SNARS version',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Toggle active status of a version.
     */
    public function toggleActive(string $id)
    {
        try {
            $version = SnarsVersion::findOrFail($id);
            $version->is_active = !$version->is_active;
            $version->updated_by = Auth::id();
            $version->save();

            if (request()->expectsJson()) {
                return response()->json([
                    'data' => $version,
                    'message' => 'Version active status toggled successfully'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Version active status toggled successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to toggle version active status',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to toggle version active status: ' . $e->getMessage());
        }
    }
}
