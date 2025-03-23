<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class SuperadminUserController extends Controller
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
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::with('tenant', 'roles');

        // Filter by tenant
        if ($request->has('tenant_id') && !empty($request->tenant_id)) {
            $query->where('tenant_id', $request->tenant_id);
        }

        // Filter by role
        if ($request->has('role') && !empty($request->role)) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by search term
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        $users = $query->paginate(10);
        $tenants = Tenant::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('superadmin.users.index', compact('users', 'tenants', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $tenants = Tenant::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('superadmin.users.create', compact('tenants', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tenant_id' => ['required', 'exists:tenants,id'],
            'role' => ['required', 'exists:roles,name'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $request->tenant_id,
            'employee_id' => $request->employee_id,
            'phone' => $request->phone,
            'position' => $request->position,
            'department' => $request->department,
            'is_active' => $request->has('is_active'),
        ]);

        // Assign role
        $user->assignRole($request->role);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = User::with('tenant', 'roles')->findOrFail($id);

        return view('superadmin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $tenants = Tenant::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $currentRole = $user->roles->first();

        return view('superadmin.users.edit', compact('user', 'tenants', 'roles', 'currentRole'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'tenant_id' => ['required', 'exists:tenants,id'],
            'role' => ['required', 'exists:roles,name'],
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
        $user->tenant_id = $request->tenant_id;
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

        // Update role
        $currentRoles = $user->getRoleNames();
        if (!$currentRoles->contains($request->role)) {
            // Hapus role yang ada
            foreach ($currentRoles as $role) {
                $user->removeRole($role);
            }
            // Assign role baru
            $user->assignRole($request->role);
        }

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Toggle user active status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('superadmin.users.index')
            ->with('success', "Pengguna berhasil {$status}.");
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Pastikan user tidak menghapus dirinya sendiri
        if (Auth::id() == $id) {
            return redirect()->route('superadmin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
