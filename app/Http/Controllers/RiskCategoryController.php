<?php

namespace App\Http\Controllers;

use App\Models\RiskCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data kategori risiko dari database
        $categories = RiskCategory::where('tenant_id', app(\App\Services\TenantSelectionService::class)->getActiveTenant()->id ?? null)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('risk-management.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil kategori untuk dropdown parent
        $parentCategories = RiskCategory::where('tenant_id', app(\App\Services\TenantSelectionService::class)->getActiveTenant()->id ?? null)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('risk-management.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:risk_categories,id',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Tambahkan tenant_id dan user info
        $tenantId = app(\App\Services\TenantSelectionService::class)->getActiveTenant()->id ?? null;
        $userId = Auth::id();

        // Buat record baru
        $category = new RiskCategory();
        $category->tenant_id = $tenantId;
        $category->name = $validated['name'];
        $category->code = $validated['code'] ?? null;
        $category->description = $validated['description'] ?? null;
        $category->parent_id = $validated['parent_id'] ?? null;
        $category->order = $validated['order'] ?? 0;
        $category->is_active = $request->has('is_active');
        $category->created_by = $userId;
        $category->updated_by = $userId;
        $category->save();

        return redirect()->route('risk-management.categories.index')
            ->with('success', 'Kategori risiko berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = RiskCategory::findOrFail($id);
        return view('risk-management.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = RiskCategory::findOrFail($id);

        // Ambil kategori untuk dropdown parent, kecuali kategori ini sendiri dan anaknya
        $parentCategories = RiskCategory::where('tenant_id', app(\App\Services\TenantSelectionService::class)->getActiveTenant()->id ?? null)
            ->whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();

        return view('risk-management.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:risk_categories,id',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Cek apakah kategori ada
        $category = RiskCategory::findOrFail($id);

        // Jangan izinkan parent_id jadi dirinya sendiri atau anaknya
        if ($validated['parent_id'] == $id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kategori tidak dapat menjadi induk dari dirinya sendiri');
        }

        // Update data
        $category->name = $validated['name'];
        $category->code = $validated['code'] ?? $category->code;
        $category->description = $validated['description'] ?? $category->description;
        $category->parent_id = $validated['parent_id'] ?? $category->parent_id;
        $category->order = $validated['order'] ?? $category->order;
        $category->is_active = $request->has('is_active');
        $category->updated_by = Auth::id();
        $category->save();

        return redirect()->route('risk-management.categories.index')
            ->with('success', 'Kategori risiko berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Cek apakah kategori ditemukan
            $category = RiskCategory::findOrFail($id);

            // Cek apakah kategori punya subkategori
            if ($category->children()->count() > 0) {
                return redirect()->route('risk-management.categories.index')
                    ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki subkategori');
            }

            // Cek apakah kategori sedang digunakan pada risiko
            if ($category->risks()->count() > 0 || $category->subCategoryRisks()->count() > 0) {
                return redirect()->route('risk-management.categories.index')
                    ->with('error', 'Kategori tidak dapat dihapus karena sedang digunakan pada data risiko');
            }

            // Hapus kategori
            $category->delete();

            return redirect()->route('risk-management.categories.index')
                ->with('success', 'Kategori risiko berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('risk-management.categories.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
