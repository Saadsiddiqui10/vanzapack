<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::whereHas('roles', fn ($q) => $q->whereIn('name', UserRole::staff()))
                ->with('roles')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['is_active' => true]), 'roles' => $this->staffRoles()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
            'is_active' => true,
            'must_change_password' => true,
            'customer_group' => 'staff',
        ]);
        $user->roles()->sync($data['roles']);

        return redirect()->route('admin.users.index')->with('success', 'Admin user created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', ['user' => $user, 'roles' => $this->staffRoles()]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        $user->roles()->sync($data['roles']);

        return redirect()->route('admin.users.index')->with('success', 'Admin user updated.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 422, 'You cannot delete your own account.');
        abort_if($user->hasRole(UserRole::SuperAdmin) && User::whereHas('roles', fn ($q) => $q->where('name', UserRole::SuperAdmin->value))->count() <= 1, 422, 'At least one super admin is required.');

        $user->delete();

        return back()->with('success', 'Admin user removed.');
    }

    private function staffRoles()
    {
        return Role::whereIn('name', UserRole::staff())->get();
    }
}
