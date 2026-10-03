<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount(['permissions', 'users'])->get(),
        ]);
    }

    public function create()
    {
        return view('admin.roles.form', [
            'role' => new Role,
            'permissions' => Permission::orderBy('group')->get()->groupBy('group'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->data($request);
        $role = Role::create(['name' => Str::slug($data['label']), 'label' => $data['label'], 'description' => $data['description'] ?? null]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.form', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::orderBy('group')->get()->groupBy('group'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $this->data($request, $role);
        $role->update(['label' => $data['label'], 'description' => $data['description'] ?? null]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        abort_if(in_array($role->name, ['super-admin', 'customer'], true), 422, 'This role is protected.');
        $role->delete();

        return back()->with('success', 'Role deleted.');
    }

    private function data(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:250'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
    }
}
