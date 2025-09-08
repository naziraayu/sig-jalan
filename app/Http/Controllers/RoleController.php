<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\RolePermission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('rolePermissions.permission')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        $permissionsGrouped = $permissions->groupBy('feature');
        return view('admin.roles.create', compact('permissionsGrouped'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        if ($request->has('permissions')) {
            foreach ($request->permissions as $permissionId) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permissionId
                ]);
            }
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function show(Role $role)
    {
        $role->load('rolePermissions.permission');
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $permissionsGrouped = $permissions->groupBy('feature');
        $rolePermissions = $role->rolePermissions->pluck('permission_id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissionsGrouped', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update([
            'name' => $request->name
        ]);

        // Hapus permission lama
        RolePermission::where('role_id', $role->id)->delete();

        // Tambah permission baru
        if ($request->has('permissions')) {
            foreach ($request->permissions as $permissionId) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permissionId
                ]);
            }
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate');
    }

    public function destroy(Role $role)
    {
        // Cek apakah role masih digunakan user
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user');
        }

        // Hapus permission terkait
        RolePermission::where('role_id', $role->id)->delete();
        
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus');
    }
}
