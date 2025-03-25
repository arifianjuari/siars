<?php

namespace App\Http\Controllers\RiskManagement;

use App\Http\Controllers\Controller;
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
        $tenantId = Auth::user()->tenant_id;
        $categories = RiskCategory::with('parent')
            ->where('tenant_id', $tenantId)
            ->get();

        return view('risk-management.settings.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $parentCategories = RiskCategory::mainCategories()
            ->where('tenant_id', $tenantId)
            ->active()
            ->get();

        return view('risk-management.settings.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:risk_categories,code',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:risk_categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Tambahkan user yang membuat dan tenant_id
        $validated['created_by'] = Auth::id();
        $validated['tenant_id'] = Auth::user()->tenant_id;

        // Default nilai is_active jika tidak diberikan
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        } else {
            $validated['is_active'] = true;
        }

        RiskCategory::create($validated);

        return redirect()->route('risk-management.settings.categories.list')
            ->with('success', 'Kategori risiko berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RiskCategory $category)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa kategori milik tenant yang sama
        if ($category->tenant_id != $tenantId) {
            return redirect()->route('risk-management.settings.categories.list')
                ->with('error', 'Anda tidak memiliki akses ke kategori ini.');
        }

        $category->load(['parent', 'children', 'creator', 'updater']);

        return view('risk-management.settings.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiskCategory $category)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa kategori milik tenant yang sama
        if ($category->tenant_id != $tenantId) {
            return redirect()->route('risk-management.settings.categories.list')
                ->with('error', 'Anda tidak memiliki akses ke kategori ini.');
        }

        // Ambil semua kategori induk, kecuali dirinya sendiri dan anaknya
        $parentCategories = RiskCategory::mainCategories()
            ->where('tenant_id', $tenantId)
            ->where('id', '!=', $category->id)
            ->whereNotIn('id', $category->children->pluck('id')->toArray())
            ->active()
            ->get();

        return view('risk-management.settings.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiskCategory $category)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa kategori milik tenant yang sama
        if ($category->tenant_id != $tenantId) {
            return redirect()->route('risk-management.settings.categories.list')
                ->with('error', 'Anda tidak memiliki akses ke kategori ini.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:risk_categories,code,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:risk_categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Tambahkan user yang mengupdate
        $validated['updated_by'] = Auth::id();

        // Default nilai is_active jika tidak diberikan
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        } else {
            $validated['is_active'] = true;
        }

        $category->update($validated);

        return redirect()->route('risk-management.settings.categories.list')
            ->with('success', 'Kategori risiko berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiskCategory $category)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa kategori milik tenant yang sama
        if ($category->tenant_id != $tenantId) {
            return redirect()->route('risk-management.settings.categories.list')
                ->with('error', 'Anda tidak memiliki akses ke kategori ini.');
        }

        // Cek apakah kategori punya anak
        if ($category->children()->count() > 0) {
            return redirect()->route('risk-management.settings.categories.list')
                ->with('error', 'Kategori tidak dapat dihapus karena memiliki subkategori.');
        }

        // Cek apakah kategori digunakan oleh risiko
        if ($category->risks()->count() > 0 || $category->subCategoryRisks()->count() > 0) {
            return redirect()->route('risk-management.settings.categories.list')
                ->with('error', 'Kategori tidak dapat dihapus karena digunakan oleh beberapa risiko.');
        }

        $category->delete();

        return redirect()->route('risk-management.settings.categories.list')
            ->with('success', 'Kategori risiko berhasil dihapus.');
    }
}
