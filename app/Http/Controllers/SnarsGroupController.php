<?php

namespace App\Http\Controllers;

use App\Models\SnarsGroup;
use App\Models\SnarsVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SnarsGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsGroup::query();

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'order');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $groups = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $groups->items(),
                'pagination' => [
                    'total' => $groups->total(),
                    'per_page' => $groups->perPage(),
                    'current_page' => $groups->currentPage(),
                    'last_page' => $groups->lastPage(),
                ],
            ]);
        }

        return view('snars.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $versions = SnarsVersion::where('is_active', true)->orderBy('version_number', 'desc')->get();
        return view('snars.groups.create', compact('versions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:snars_groups,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'version_id' => 'required|exists:snars_versions,id',
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
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        if (!isset($data['order'])) {
            // Get the highest order and add 1
            $maxOrder = SnarsGroup::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $group = SnarsGroup::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $group, 'message' => 'Group created successfully'], 201);
        }

        return redirect()->route('snars.groups.index')
            ->with('success', 'Group created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $group = SnarsGroup::with('chapters')->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $group]);
        }

        return view('snars.groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $group = SnarsGroup::findOrFail($id);
        $versions = SnarsVersion::orderBy('version_number', 'desc')->get();
        return view('snars.groups.edit', compact('group', 'versions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $group = SnarsGroup::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:50', Rule::unique('snars_groups', 'code')->ignore($group->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'version_id' => 'required|exists:snars_versions,id',
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
            $data['is_active'] = $group->is_active;
        }

        $group->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $group, 'message' => 'Group updated successfully']);
        }

        return redirect()->route('snars.groups.index')
            ->with('success', 'Group updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = SnarsGroup::findOrFail($id);

        // Check if the group has any chapters
        if ($group->chapters()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete group because it has associated chapters. Remove the chapters first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete group because it has associated chapters. Remove the chapters first.');
        }

        $group->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Group deleted successfully']);
        }

        return redirect()->route('snars.groups.index')
            ->with('success', 'Group deleted successfully');
    }

    /**
     * Update the order of multiple groups.
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'groups' => 'required|array',
            'groups.*.id' => 'required|string|exists:snars_groups,id',
            'groups.*.order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $groups = $request->input('groups');

        foreach ($groups as $groupData) {
            $group = SnarsGroup::findOrFail($groupData['id']);
            $group->update([
                'order' => $groupData['order'],
                'updated_by' => Auth::id(),
            ]);
        }

        return response()->json(['message' => 'Group order updated successfully']);
    }

    /**
     * Toggle the active status of a group.
     */
    public function toggleActive(string $id)
    {
        $group = SnarsGroup::findOrFail($id);
        $group->update([
            'is_active' => !$group->is_active,
            'updated_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $group,
                'message' => 'Group status updated successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Group status updated successfully');
    }
}
