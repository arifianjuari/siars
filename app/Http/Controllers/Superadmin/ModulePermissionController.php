<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Module;

class ModulePermissionController extends Controller
{
    /**
     * Menampilkan halaman manajemen izin modul
     */
    public function index()
    {
        $roles = Role::all();
        $modules = Module::all();

        // Ambil semua izin yang sudah ada
        $permissions = DB::table('role_module_permissions')
            ->join('roles', 'role_module_permissions.role_id', '=', 'roles.id')
            ->join('modules', 'role_module_permissions.module_id', '=', 'modules.id')
            ->select(
                'role_module_permissions.*',
                'roles.name as role_name',
                'modules.name as module_name',
                'modules.code as module_code'
            )
            ->get();

        // Format data untuk tampilan
        $permissionsByRole = [];
        foreach ($permissions as $permission) {
            $permissionsByRole[$permission->role_id][$permission->module_id] = [
                'can_view' => $permission->can_view,
                'can_create' => $permission->can_create,
                'can_edit' => $permission->can_edit,
                'can_delete' => $permission->can_delete,
                'can_approve' => $permission->can_approve,
                'can_activate' => $permission->can_activate,
            ];
        }

        return view('superadmin.module-permissions.index', compact('roles', 'modules', 'permissionsByRole'));
    }

    /**
     * Menampilkan halaman edit izin untuk sebuah role dan modul
     */
    public function edit($roleId, $moduleId)
    {
        $role = Role::findOrFail($roleId);
        $module = Module::findOrFail($moduleId);

        $permission = DB::table('role_module_permissions')
            ->where('role_id', $roleId)
            ->where('module_id', $moduleId)
            ->first();

        // Jika belum ada izin, buat array default
        if (!$permission) {
            $permission = [
                'can_view' => false,
                'can_create' => false,
                'can_edit' => false,
                'can_delete' => false,
                'can_approve' => false,
                'can_activate' => false,
            ];
        }

        return view('superadmin.module-permissions.edit', compact('role', 'module', 'permission'));
    }

    /**
     * Update izin modul untuk role tertentu
     */
    public function update(Request $request, $roleId, $moduleId)
    {
        $request->validate([
            'can_view' => 'boolean',
            'can_create' => 'boolean',
            'can_edit' => 'boolean',
            'can_delete' => 'boolean',
            'can_approve' => 'boolean',
            'can_activate' => 'boolean',
        ]);

        // Persiapkan data
        $data = [
            'can_view' => $request->has('can_view'),
            'can_create' => $request->has('can_create'),
            'can_edit' => $request->has('can_edit'),
            'can_delete' => $request->has('can_delete'),
            'can_approve' => $request->has('can_approve'),
            'can_activate' => $request->has('can_activate'),
            'updated_at' => now(),
        ];

        // Cek apakah sudah ada izin
        $exists = DB::table('role_module_permissions')
            ->where('role_id', $roleId)
            ->where('module_id', $moduleId)
            ->exists();

        if ($exists) {
            // Update izin yang sudah ada
            DB::table('role_module_permissions')
                ->where('role_id', $roleId)
                ->where('module_id', $moduleId)
                ->update($data);
        } else {
            // Tambahkan izin baru
            $data['role_id'] = $roleId;
            $data['module_id'] = $moduleId;
            $data['created_at'] = now();

            DB::table('role_module_permissions')->insert($data);
        }

        return redirect()
            ->route('superadmin.module-permissions.index')
            ->with('success', 'Izin modul berhasil diperbarui');
    }

    /**
     * Atur izin untuk semua modul sekaligus
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.role_id' => 'required|exists:roles,id',
            'permissions.*.module_id' => 'required|exists:modules,id',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
            'permissions.*.can_approve' => 'boolean',
            'permissions.*.can_activate' => 'boolean',
        ]);

        $now = now();

        foreach ($request->permissions as $permission) {
            $roleId = $permission['role_id'];
            $moduleId = $permission['module_id'];

            $data = [
                'can_view' => $permission['can_view'] ?? false,
                'can_create' => $permission['can_create'] ?? false,
                'can_edit' => $permission['can_edit'] ?? false,
                'can_delete' => $permission['can_delete'] ?? false,
                'can_approve' => $permission['can_approve'] ?? false,
                'can_activate' => $permission['can_activate'] ?? false,
                'updated_at' => $now,
            ];

            $exists = DB::table('role_module_permissions')
                ->where('role_id', $roleId)
                ->where('module_id', $moduleId)
                ->exists();

            if ($exists) {
                DB::table('role_module_permissions')
                    ->where('role_id', $roleId)
                    ->where('module_id', $moduleId)
                    ->update($data);
            } else {
                $data['role_id'] = $roleId;
                $data['module_id'] = $moduleId;
                $data['created_at'] = $now;

                DB::table('role_module_permissions')->insert($data);
            }
        }

        return response()->json(['message' => 'Izin modul berhasil diperbarui']);
    }
}
