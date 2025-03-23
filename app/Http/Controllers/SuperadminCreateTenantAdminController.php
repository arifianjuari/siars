<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class SuperadminCreateTenantAdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\CheckRole::class . ':Superadmin');
    }

    /**
     * Show the form for creating a new tenant admin.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\View\View
     */
    public function create(Tenant $tenant)
    {
        // Pastikan tenant aktif
        if (!$tenant->is_active) {
            return redirect()->route('superadmin.tenants.index')
                ->with('error', 'Tidak dapat membuat admin untuk rumah sakit yang tidak aktif.');
        }

        return view('superadmin.tenant-admin.create', compact('tenant'));
    }

    /**
     * Store a newly created tenant admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Tenant $tenant)
    {
        // Pastikan tenant aktif
        if (!$tenant->is_active) {
            return redirect()->route('superadmin.tenants.index')
                ->with('error', 'Tidak dapat membuat admin untuk rumah sakit yang tidak aktif.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
        ]);

        // Cek apakah sudah ada admin untuk tenant ini
        $existingAdmin = User::role('TenantAdmin')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->exists();

        if ($existingAdmin) {
            return redirect()->back()->withInput()
                ->with('warning', 'Rumah sakit ini sudah memiliki admin yang aktif. Anda tetap bisa menambahkan admin baru.');
        }

        // Buat user baru dengan role TenantAdmin
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenant->id,
            'employee_id' => $request->employee_id,
            'phone' => $request->phone,
            'position' => $request->position,
            'department' => $request->department,
            'is_active' => $request->has('is_active'),
        ]);

        // Assign role TenantAdmin
        $user->assignRole('TenantAdmin');

        return redirect()->route('superadmin.tenants.show', $tenant)
            ->with('success', 'Admin rumah sakit berhasil ditambahkan.');
    }

    /**
     * Display the list of tenant admins for a specific tenant.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\View\View
     */
    public function index(Tenant $tenant)
    {
        $tenantAdmins = User::role('TenantAdmin')
            ->where('tenant_id', $tenant->id)
            ->get();

        return view('superadmin.tenant-admin.index', compact('tenant', 'tenantAdmins'));
    }

    /**
     * Show the form for editing a tenant admin.
     *
     * @param  \App\Models\Tenant  $tenant
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(Tenant $tenant, User $user)
    {
        // Pastikan user adalah TenantAdmin untuk tenant ini
        if ($user->tenant_id != $tenant->id || !$user->hasRole('TenantAdmin')) {
            return redirect()->route('superadmin.tenant-admin.index', $tenant)
                ->with('error', 'Pengguna yang dipilih bukan admin untuk rumah sakit ini.');
        }

        return view('superadmin.tenant-admin.edit', compact('tenant', 'user'));
    }

    /**
     * Update the specified tenant admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tenant  $tenant
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tenant $tenant, User $user)
    {
        // Pastikan user adalah TenantAdmin untuk tenant ini
        if ($user->tenant_id != $tenant->id || !$user->hasRole('TenantAdmin')) {
            return redirect()->route('superadmin.tenant-admin.index', $tenant)
                ->with('error', 'Pengguna yang dipilih bukan admin untuk rumah sakit ini.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
        ];

        // Hanya validasi password jika diisi
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->employee_id = $request->employee_id;
        $user->phone = $request->phone;
        $user->position = $request->position;
        $user->department = $request->department;
        $user->is_active = $request->has('is_active');

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('superadmin.tenant-admin.index', $tenant)
            ->with('success', 'Admin rumah sakit berhasil diperbarui.');
    }

    /**
     * Toggle the active status of a tenant admin.
     *
     * @param  \App\Models\Tenant  $tenant
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(Tenant $tenant, User $user)
    {
        // Pastikan user adalah TenantAdmin untuk tenant ini
        if ($user->tenant_id != $tenant->id || !$user->hasRole('TenantAdmin')) {
            return redirect()->route('superadmin.tenant-admin.index', $tenant)
                ->with('error', 'Pengguna yang dipilih bukan admin untuk rumah sakit ini.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('superadmin.tenant-admin.index', $tenant)
            ->with('success', "Admin rumah sakit berhasil {$status}.");
    }

    /**
     * Remove a tenant admin.
     *
     * @param  \App\Models\Tenant  $tenant
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tenant $tenant, User $user)
    {
        // Pastikan user adalah TenantAdmin untuk tenant ini
        if ($user->tenant_id != $tenant->id || !$user->hasRole('TenantAdmin')) {
            return redirect()->route('superadmin.tenant-admin.index', $tenant)
                ->with('error', 'Pengguna yang dipilih bukan admin untuk rumah sakit ini.');
        }

        $user->delete();

        return redirect()->route('superadmin.tenant-admin.index', $tenant)
            ->with('success', 'Admin rumah sakit berhasil dihapus.');
    }
}
