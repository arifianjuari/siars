<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\TenantSelectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TenantController extends Controller
{
    /**
     * Service untuk pemilihan tenant
     *
     * @var TenantSelectionService
     */
    protected $tenantSelectionService;

    /**
     * Konstruktor
     *
     * @param TenantSelectionService $tenantSelectionService
     */
    public function __construct(TenantSelectionService $tenantSelectionService)
    {
        $this->tenantSelectionService = $tenantSelectionService;
    }

    /**
     * Tampilkan halaman untuk memilih tenant bagi superadmin
     *
     * @return \Illuminate\View\View
     */
    public function selectTenant()
    {
        $user = Auth::user();
        $accessibleTenants = $user->accessibleTenants;

        return view('tenant.select', [
            'tenants' => $accessibleTenants
        ]);
    }

    /**
     * Set tenant aktif untuk user saat ini
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setActiveTenant(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $user = Auth::user();

        // Validasi akses tenant
        if (!$this->tenantSelectionService->userCanAccessTenant($user->id, $tenantId)) {
            return redirect()->back()
                ->withErrors(['tenant' => 'Anda tidak memiliki akses ke rumah sakit ini']);
        }

        // Set tenant aktif
        $this->tenantSelectionService->setActiveTenant($tenantId);

        // Set default tenant jika diminta dan user adalah superadmin
        if ($request->has('set_default') && $user->isSuperadmin()) {
            $this->tenantSelectionService->updateSuperadminDefaultTenant($user->id, $tenantId);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Rumah sakit berhasil dipilih');
    }

    /**
     * Tambahkan tenant baru (hanya untuk superadmin)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:tenants,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        // Tambahkan created_by dan is_active
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;

        $tenant = Tenant::create($validated);

        // Tambahkan akses untuk superadmin yang membuat
        $user = Auth::user();
        if ($user) {
            $user->accessibleTenants()->attach($tenant->id);
        }

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Rumah sakit berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit tenant
     *
     * @param Tenant $tenant
     * @return \Illuminate\View\View
     */
    public function edit(Tenant $tenant)
    {
        return view('superadmin.tenants.edit', [
            'tenant' => $tenant
        ]);
    }

    /**
     * Update informasi tenant
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tenants,code,' . $tenant->id,
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:100',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload dan simpan file logo jika ada
        if ($request->hasFile('logo_path')) {
            $logoFile = $request->file('logo_path');
            $filename = time() . '_' . $logoFile->getClientOriginalName();

            // Simpan ke folder storage/app/public/tenant-logos
            $path = $logoFile->storeAs('tenant-logos', $filename, 'public');

            // Set nilai logo_path untuk disimpan ke database
            $validated['logo_path'] = $path;
        }

        // Tambahkan updated_by ke data yang akan disimpan
        $validated['updated_by'] = Auth::id();

        $tenant->update($validated);

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Informasi rumah sakit berhasil diperbarui');
    }

    /**
     * Nonaktifkan tenant (soft delete)
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tenant $tenant)
    {
        try {
            // Jika tenant memiliki user aktif, berikan pesan error
            if ($tenant->users()->where('users.is_active', true)->count() > 0) {
                return redirect()->back()
                    ->withErrors(['users' => 'Rumah sakit ini masih memiliki pengguna aktif']);
            }

            // Hapus akses superadmin ke tenant ini
            $tenant->users()->update(['is_active' => false]);

            // Nonaktifkan tenant
            $tenant->is_active = false;
            $tenant->save();

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Rumah sakit berhasil dinonaktifkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus rumah sakit: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus tenant secara permanen (hard delete)
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDestroy(Request $request, $id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            // Hapus semua relasi dengan tenant ini
            $tenant->users()->update(['tenant_id' => null]);

            // Hapus akses superadmin ke tenant ini
            $superadmins = User::role('Superadmin')->get();
            foreach ($superadmins as $admin) {
                $admin->accessibleTenants()->detach($tenant->id);
            }

            // Hard delete tenant
            $tenant->delete();

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Rumah sakit berhasil dihapus permanen');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus rumah sakit secara permanen: ' . $e->getMessage()]);
        }
    }

    /**
     * Mengaktifkan kembali tenant yang dinonaktifkan
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request, $id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            // Aktifkan tenant
            $tenant->is_active = true;
            $tenant->save();

            // Tambahkan akses untuk semua superadmin
            $superadmins = User::role('Superadmin')->get();
            foreach ($superadmins as $admin) {
                // Cek apakah relasi sudah ada
                if (!$admin->accessibleTenants()->where('tenant_id', $tenant->id)->exists()) {
                    $admin->accessibleTenants()->attach($tenant->id);
                }
            }

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Rumah sakit berhasil diaktifkan kembali');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengaktifkan rumah sakit: ' . $e->getMessage()]);
        }
    }

    /**
     * Menampilkan detail tenant
     *
     * @param Tenant $tenant
     * @return \Illuminate\View\View
     */
    public function show(Tenant $tenant)
    {
        return view('superadmin.tenants.show', [
            'tenant' => $tenant
        ]);
    }
}
