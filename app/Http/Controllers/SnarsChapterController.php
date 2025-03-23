<?php

namespace App\Http\Controllers;

use App\Models\SnarsChapter;
use App\Models\SnarsGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SnarsChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsChapter::with('group');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('snars_group_id')) {
            $query->where('snars_group_id', $request->input('snars_group_id'));
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
        $chapters = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $chapters->items(),
                'pagination' => [
                    'total' => $chapters->total(),
                    'per_page' => $chapters->perPage(),
                    'current_page' => $chapters->currentPage(),
                    'last_page' => $chapters->lastPage(),
                ],
            ]);
        }

        $groups = SnarsGroup::active()->ordered()->get();
        return view('snars.chapters.index', compact('chapters', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = SnarsGroup::active()->ordered()->get();
        return view('snars.chapters.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'snars_group_id' => 'required|string|exists:snars_groups,id',
            'code' => 'required|string|max:50|unique:snars_chapters,code',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
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

        // Set default values if not provided
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        if (!isset($data['order'])) {
            // Get the highest order for chapters in the same group and add 1
            $maxOrder = SnarsChapter::where('snars_group_id', $data['snars_group_id'])->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $chapter = SnarsChapter::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $chapter, 'message' => 'Chapter created successfully'], 201);
        }

        return redirect()->route('snars.chapters.index')
            ->with('success', 'Chapter created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $chapter = SnarsChapter::with(['group', 'standards'])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $chapter]);
        }

        return view('snars.chapters.show', compact('chapter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $chapter = SnarsChapter::findOrFail($id);
        $groups = SnarsGroup::active()->ordered()->get();
        return view('snars.chapters.edit', compact('chapter', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $chapter = SnarsChapter::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'snars_group_id' => 'required|string|exists:snars_groups,id',
            'code' => ['required', 'string', 'max:50', Rule::unique('snars_chapters', 'code')->ignore($chapter->id)],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
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
            $data['is_active'] = $chapter->is_active;
        }

        $chapter->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $chapter, 'message' => 'Chapter updated successfully']);
        }

        return redirect()->route('snars.chapters.index')
            ->with('success', 'Chapter updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $chapter = SnarsChapter::findOrFail($id);

        // Check if the chapter has any standards
        if ($chapter->standards()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete chapter because it has associated standards. Remove the standards first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete chapter because it has associated standards. Remove the standards first.');
        }

        $chapter->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Chapter deleted successfully']);
        }

        return redirect()->route('snars.chapters.index')
            ->with('success', 'Chapter deleted successfully');
    }

    /**
     * Update the order of multiple chapters.
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chapters' => 'required|array',
            'chapters.*.id' => 'required|string|exists:snars_chapters,id',
            'chapters.*.order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $chapters = $request->input('chapters');

        foreach ($chapters as $chapterData) {
            $chapter = SnarsChapter::findOrFail($chapterData['id']);
            $chapter->update([
                'order' => $chapterData['order'],
                'updated_by' => Auth::id(),
            ]);
        }

        return response()->json(['message' => 'Chapter order updated successfully']);
    }

    /**
     * Toggle the active status of a chapter.
     */
    public function toggleActive(string $id)
    {
        $chapter = SnarsChapter::findOrFail($id);
        $chapter->update([
            'is_active' => !$chapter->is_active,
            'updated_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $chapter,
                'message' => 'Chapter status updated successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Chapter status updated successfully');
    }
}
