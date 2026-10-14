<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::withCount('users')
            ->with('permissions')
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->get();

        $allPermissions = Permission::orderBy('group')->orderBy('name')->get();
        $groupedPermissions = $allPermissions->groupBy('group');

        $totalRoles = $roles->count();
        $systemRolesCount = $roles->where('is_system', true)->count();
        $customRolesCount = $roles->where('is_system', false)->count();
        $totalPermissions = $allPermissions->count();

        return view('admin.roles.index', compact(
            'roles',
            'groupedPermissions',
            'allPermissions',
            'totalRoles',
            'systemRolesCount',
            'customRolesCount',
            'totalPermissions'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:roles,name'],
            'display_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => Str::slug($validated['name'], '_'),
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        if (! empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')->with('success', "Peran baru '{$role->display_name}' berhasil dibuat dengan wewenang yang dipilih.");
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Jika super_admin, pastikan tetap memiliki semua hak akses
        if ($role->name === 'super_admin') {
            $allPermIds = Permission::pluck('id')->toArray();
            $role->permissions()->sync($allPermIds);
        } else {
            $role->permissions()->sync($validated['permissions'] ?? []);
        }

        return redirect()->route('admin.roles.index')->with('success', "Peran '{$role->display_name}' dan hak aksesnya berhasil diperbarui.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->withErrors(['error' => "Peran sistem bawaan '{$role->display_name}' tidak dapat dihapus."]);
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => "Peran '{$role->display_name}' masih terhubung dengan {$role->users()->count()} pengguna. Silakan pindahkan pengguna terlebih dahulu sebelum menghapus."]);
        }

        $name = $role->display_name;
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', "Peran '{$name}' telah berhasil dihapus.");
    }
}
