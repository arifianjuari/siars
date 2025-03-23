<?php

namespace App\Http\Controllers;

use App\Models\SnarsStandard;
use App\Models\SnarsChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SnarsStandardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SnarsStandard::with(['chapter', 'chapter.group']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        if ($request->has('chapter_id')) {
            $query->where('chapter_id', $request->input('chapter_id'));
        }

        if ($request->has('group_id')) {
            $chapterIds = SnarsChapter::where('group_id', $request->input('group_id'))->pluck('id');
            $query->whereIn('chapter_id', $chapterIds);
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
        $standards = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $standards->items(),
                'pagination' => [
                    'total' => $standards->total(),
                    'per_page' => $standards->perPage(),
                    'current_page' => $standards->currentPage(),
                    'last_page' => $standards->lastPage(),
                ],
            ]);
        }

        $chapters = SnarsChapter::with('group')->active()->ordered()->get();
        $groups = \App\Models\SnarsGroup::active()->ordered()->get();
        return view('snars.standards.index', compact('standards', 'chapters', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $chapters = SnarsChapter::with('group')->active()->ordered()->get();
        return view('snars.standards.create', compact('chapters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chapter_id' => 'required|string|exists:snars_chapters,id',
            'group_id' => 'required|string|exists:snars_groups,id',
            'code' => 'required|string|max:50|unique:snars_standards,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purpose' => 'nullable|string',
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
            // Get the highest order for standards in the same chapter and add 1
            $maxOrder = SnarsStandard::where('chapter_id', $data['chapter_id'])->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        // Pastikan group_id ada di data
        if (!isset($data['group_id'])) {
            // Jika tidak ada, ambil dari chapter
            $chapter = SnarsChapter::find($data['chapter_id']);
            if ($chapter) {
                $data['group_id'] = $chapter->group_id;
            } else {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => ['chapter_id' => ['Invalid chapter selected']]], 422);
                }
                return redirect()->back()->withErrors(['chapter_id' => 'Invalid chapter selected'])->withInput();
            }
        }

        $standard = SnarsStandard::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $standard, 'message' => 'Standard created successfully'], 201);
        }

        // Jika request berasal dari halaman chapter, redirect kembali ke detail chapter
        if ($request->has('chapter_id') && !empty($request->chapter_id)) {
            $chapter = SnarsChapter::find($request->chapter_id);
            if ($chapter) {
                return redirect()->route('snars.chapters.show', $chapter)
                    ->with('success', 'Standar berhasil dibuat');
            }
        }

        // Redirect ke halaman detail standar yang baru dibuat
        return redirect()->route('snars.standards.show', $standard)
            ->with('success', 'Standar berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $standard = SnarsStandard::with(['chapter', 'chapter.group', 'assessmentElements'])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $standard]);
        }

        return view('snars.standards.show', compact('standard'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $standard = SnarsStandard::findOrFail($id);
        $chapters = SnarsChapter::with('group')->active()->ordered()->get();
        return view('snars.standards.edit', compact('standard', 'chapters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $standard = SnarsStandard::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'chapter_id' => 'required|string|exists:snars_chapters,id',
            'group_id' => 'required|string|exists:snars_groups,id',
            'code' => ['required', 'string', 'max:50', Rule::unique('snars_standards', 'code')->ignore($standard->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purpose' => 'nullable|string',
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
            $data['is_active'] = $standard->is_active;
        }

        // Pastikan group_id ada di data
        if (!isset($data['group_id'])) {
            // Jika tidak ada, ambil dari chapter
            $chapter = SnarsChapter::find($data['chapter_id']);
            if ($chapter) {
                $data['group_id'] = $chapter->group_id;
            } else {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => ['chapter_id' => ['Invalid chapter selected']]], 422);
                }
                return redirect()->back()->withErrors(['chapter_id' => 'Invalid chapter selected'])->withInput();
            }
        }

        $standard->update($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $standard, 'message' => 'Standard updated successfully']);
        }

        // Redirect ke halaman detail standar yang sudah diupdate
        return redirect()->route('snars.standards.show', $standard)
            ->with('success', 'Standar berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $standard = SnarsStandard::findOrFail($id);

        // Check if the standard has any assessment elements
        if ($standard->assessmentElements()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete standard because it has associated assessment elements. Remove the assessment elements first.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete standard because it has associated assessment elements. Remove the assessment elements first.');
        }

        $standard->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Standard deleted successfully']);
        }

        return redirect()->route('snars.standards.index')
            ->with('success', 'Standard deleted successfully');
    }

    /**
     * Update the order of multiple standards.
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'standards' => 'required|array',
            'standards.*.id' => 'required|string|exists:snars_standards,id',
            'standards.*.order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $standards = $request->input('standards');

        foreach ($standards as $standardData) {
            $standard = SnarsStandard::findOrFail($standardData['id']);
            $standard->update([
                'order' => $standardData['order'],
                'updated_by' => Auth::id(),
            ]);
        }

        return response()->json(['message' => 'Standard order updated successfully']);
    }

    /**
     * Toggle the active status of a standard.
     */
    public function toggleActive(string $id)
    {
        $standard = SnarsStandard::findOrFail($id);
        $standard->update([
            'is_active' => !$standard->is_active,
            'updated_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $standard,
                'message' => 'Standard status updated successfully',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Standard status updated successfully');
    }
}
