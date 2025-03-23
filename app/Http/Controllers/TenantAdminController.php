<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Services\TenantSelectionService;

class TenantAdminController extends Controller
{
    protected $tenantService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TenantSelectionService $tenantService)
    {
        $this->middleware('auth');
        $this->middleware('role:TenantAdmin');
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of the users for the current tenant.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terkait dengan rumah sakit manapun.');
        }

        $query = User::with('roles')
            ->where('tenant_id', $tenant->id);

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

        // Exclude superadmins and other tenant admins
        $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Superadmin');
        });

        $users = $query->paginate(10);
        $roles = Role::where('name', '!=', 'Superadmin')
            ->where('name', '!=', 'TenantAdmin')
            ->orderBy('name')
            ->get();

        return view('tenantadmin.users.index', compact('users', 'roles', 'tenant'));
    }

    /**
     * Show the form for creating a new user in the tenant.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terkait dengan rumah sakit manapun.');
        }

        $roles = Role::where('name', '!=', 'Superadmin')
            ->where('name', '!=', 'TenantAdmin')
            ->orderBy('name')
            ->get();

        return view('tenantadmin.users.create', compact('roles', 'tenant'));
    }

    /**
     * Store a newly created user in the tenant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terkait dengan rumah sakit manapun.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
        ]);

        // Validasi role yang dipilih (tidak boleh Superadmin atau TenantAdmin)
        if ($request->role == 'Superadmin' || $request->role == 'TenantAdmin') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda tidak diperbolehkan membuat pengguna dengan role tersebut.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenant->id, // Selalu gunakan tenant dari admin yang membuat
            'employee_id' => $request->employee_id,
            'phone' => $request->phone,
            'position' => $request->position,
            'department' => $request->department,
            'is_active' => $request->has('is_active'),
        ]);

        // Assign role
        $user->assignRole($request->role);

        return redirect()->route('tenantadmin.users.index')
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
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        $user = User::with('roles')
            ->where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->firstOrFail();

        // Pastikan pengguna hanya bisa melihat pengguna dari rumah sakit yang sama
        if ($user->tenant_id !== $tenant->id) {
            return redirect()->route('tenantadmin.users.index')
                ->with('error', 'Anda tidak memiliki akses untuk melihat pengguna ini.');
        }

        return view('tenantadmin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        $user = User::with('roles')
            ->where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->firstOrFail();

        // Pastikan pengguna hanya bisa mengedit pengguna dari rumah sakit yang sama
        if ($user->tenant_id !== $tenant->id) {
            return redirect()->route('tenantadmin.users.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit pengguna ini.');
        }

        $roles = Role::where('name', '!=', 'Superadmin')
            ->where('name', '!=', 'TenantAdmin')
            ->orderBy('name')
            ->get();
        $currentRole = $user->roles->first();

        return view('tenantadmin.users.edit', compact('user', 'roles', 'currentRole', 'tenant'));
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
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        $user = User::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->firstOrFail();

        // Pastikan pengguna hanya bisa mengedit pengguna dari rumah sakit yang sama
        if ($user->tenant_id !== $tenant->id) {
            return redirect()->route('tenantadmin.users.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit pengguna ini.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
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

        // Validasi role yang dipilih (tidak boleh Superadmin atau TenantAdmin)
        if ($request->role == 'Superadmin' || $request->role == 'TenantAdmin') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda tidak diperbolehkan mengubah pengguna menjadi role tersebut.');
        }

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

        return redirect()->route('tenantadmin.users.index')
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
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        $user = User::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->firstOrFail();

        // Pastikan pengguna hanya bisa menonaktifkan pengguna dari rumah sakit yang sama
        if ($user->tenant_id !== $tenant->id) {
            return redirect()->route('tenantadmin.users.index')
                ->with('error', 'Anda tidak memiliki akses untuk menonaktifkan pengguna ini.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('tenantadmin.users.index')
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
        $currentUser = Auth::user();

        // Pastikan user tidak menghapus dirinya sendiri
        if (Auth::id() == $id) {
            return redirect()->route('tenantadmin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $tenant = $currentUser->tenant;

        $user = User::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->firstOrFail();

        // Pastikan pengguna hanya bisa menghapus pengguna dari rumah sakit yang sama
        if ($user->tenant_id !== $tenant->id) {
            return redirect()->route('tenantadmin.users.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus pengguna ini.');
        }

        $user->delete();

        return redirect()->route('tenantadmin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Tampilkan dashboard informasi rumah sakit
     */
    public function dashboard()
    {
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terkait dengan rumah sakit manapun.');
        }

        // Ambil statistik pengguna berdasarkan role
        $userStats = User::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->withCount('roles')
            ->get()
            ->groupBy(function ($user) {
                return $user->roles->first()->name ?? 'Tanpa Role';
            })
            ->map->count();

        // Ambil modul yang aktif untuk rumah sakit
        $activatedModules = $tenant->modules()->wherePivot('is_active', true)->get();

        return view('tenantadmin.dashboard', compact('tenant', 'userStats', 'activatedModules'));
    }

    /**
     * Tampilkan dan kelola modul rumah sakit
     */
    public function modules()
    {
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terkait dengan rumah sakit manapun.');
        }

        // Ambil modul yang aktif untuk rumah sakit
        $activeModules = $tenant->modules()->wherePivot('is_active', true)->get();

        // Ambil permintaan modul yang sedang pending
        $pendingRequests = $tenant->moduleActivationRequests()->where('status', 'pending')->with('module')->get();

        // Ambil semua modul dari database
        $allModules = \App\Models\Module::where('is_active', true)->get();

        // Filter untuk mendapatkan modul yang tersedia tetapi belum aktif/pending
        $availableModules = $allModules->filter(function ($module) use ($activeModules, $pendingRequests) {
            // Cek apakah modul sudah aktif
            $isActive = $activeModules->contains('id', $module->id);

            // Cek apakah modul sedang pending
            $isPending = $pendingRequests->contains(function ($request) use ($module) {
                return $request->module_id == $module->id;
            });

            // Modul tersedia jika tidak aktif dan tidak pending
            return !$isActive && !$isPending;
        });

        return view('tenantadmin.modules', [
            'tenant' => $tenant,
            'activeModules' => $activeModules,
            'pendingRequests' => $pendingRequests,
            'availableModules' => $availableModules
        ]);
    }

    /**
     * Request module activation
     */
    public function requestModuleActivation(Request $request)
    {
        $currentUser = Auth::user();
        $tenant = $currentUser->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terkait dengan rumah sakit manapun.');
        }

        $request->validate([
            'module_id' => ['required', 'exists:modules,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Periksa apakah modul sudah aktif
        $moduleAlreadyActive = $tenant->modules()
            ->wherePivot('module_id', $request->module_id)
            ->wherePivot('is_active', true)
            ->exists();

        if ($moduleAlreadyActive) {
            return redirect()->back()
                ->with('error', 'Modul ini sudah aktif untuk rumah sakit Anda.');
        }

        // Periksa apakah permintaan untuk modul ini sudah ada yang pending
        $pendingRequest = $tenant->moduleActivationRequests()
            ->where('module_id', $request->module_id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingRequest) {
            return redirect()->back()
                ->with('error', 'Sudah ada permintaan aktivasi yang tertunda untuk modul ini.');
        }

        // Buat permintaan aktivasi baru
        $tenant->moduleActivationRequests()->create([
            'module_id' => $request->module_id,
            'requested_by' => $currentUser->id,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('tenantadmin.modules')
            ->with('success', 'Permintaan aktivasi modul berhasil dikirim dan sedang menunggu persetujuan.');
    }
}
