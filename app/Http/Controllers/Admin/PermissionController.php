<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $groupFilter = $request->get('group');

        $query = Permission::with('roles')->withCount('roles');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('group', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($groupFilter) {
            $query->where('group', $groupFilter);
        }

        $permissions = $query->orderBy('group')->orderBy('name')->get();
        $groupedPermissions = $permissions->groupBy('group');

        $distinctGroups = Permission::select('group')->distinct()->pluck('group');
        $totalPermissions = Permission::count();
        $totalGroups = $distinctGroups->count();
        $totalRoles = Role::count();

        return view('admin.permissions.index', compact(
            'permissions',
            'groupedPermissions',
            'distinctGroups',
            'totalPermissions',
            'totalGroups',
            'totalRoles',
            'search',
            'groupFilter'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'regex:/^[a-zA-Z0-9_\.-]+$/', 'unique:permissions,name'],
            'display_name' => ['required', 'string', 'max:120'],
            'group' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $permission = Permission::create([
            'name' => Str::lower($validated['name']),
            'display_name' => $validated['display_name'],
            'group' => trim($validated['group']),
            'description' => $validated['description'] ?? null,
        ]);

        // Berikan izin ini secara otomatis ke peran Super Admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->syncWithoutDetaching([$permission->id]);
        }

        return redirect()->route('admin.permissions.index')->with('success', "Hak akses '{$permission->display_name}' ({$permission->name}) berhasil didaftarkan.");
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:120'],
            'group' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $permission->update([
            'display_name' => $validated['display_name'],
            'group' => trim($validated['group']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', "Hak akses '{$permission->name}' berhasil diperbarui.");
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $name = $permission->name;
        $permission->roles()->detach();
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', "Hak akses '{$name}' telah dihapus dari sistem.");
    }
}
