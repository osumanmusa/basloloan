<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('view-roles');

        $roles = Role::with(['permissions', 'users'])
            ->withCount('users')
            ->get();

        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('-', $permission->name)[0];
        });

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create-roles');

        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => 'web'
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'Role created successfully!');
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('edit-roles');

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete-roles');

        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role that has users assigned.');
        }

        if ($role->name === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete admin role.');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully!');
    }
}