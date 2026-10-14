<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $roleId = $request->get('role_id');
        $status = $request->get('status');

        $query = User::with('roleModel')->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($roleId) {
            $query->where('role_id', $roleId);
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        $users = $query->paginate(12)->withQueryString();
        $roles = Role::orderBy('name')->get();

        // Statistik ringkas
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $superAdminCount = User::where('role', 'super_admin')
            ->orWhereHas('roleModel', fn ($q) => $q->where('name', 'super_admin'))
            ->count();
        $staffCount = $totalUsers - $superAdminCount;

        return view('admin.users.index', compact(
            'users',
            'roles',
            'totalUsers',
            'activeUsers',
            'superAdminCount',
            'staffCount',
            'search',
            'roleId',
            'status'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        User::create([
            'name' => $validated['name'],
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
            'role' => $role->name === 'super_admin' ? 'super_admin' : 'admin',
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')->with('success', "Pengguna baru {$validated['name']} berhasil ditambahkan.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::min(8), 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        // Proteksi: jangan sampai super admin satu-satunya diturunkan peran atau dinonaktifkan
        $isLastSuperAdmin = $user->isSuperAdmin() &&
            User::where(fn ($q) => $q->where('role', 'super_admin')->orWhere('role_id', $role->id))->count() <= 1;

        if ($isLastSuperAdmin && ($role->name !== 'super_admin' || ! $request->boolean('is_active', true))) {
            return back()->withErrors(['role_id' => 'Tidak dapat mengubah peran atau menonaktifkan akun Super Admin terakhir di sistem.']);
        }

        $data = [
            'name' => $validated['name'],
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $role->id,
            'role' => $role->name === 'super_admin' ? 'super_admin' : 'admin',
            'is_active' => $request->boolean('is_active', true),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif digunakan.']);
        }

        if ($user->isSuperAdmin() && $user->is_active) {
            $superAdminsCount = User::where('is_active', true)
                ->where(fn ($q) => $q->where('role', 'super_admin')->orWhereHas('roleModel', fn ($r) => $r->where('name', 'super_admin')))
                ->count();

            if ($superAdminsCount <= 1) {
                return back()->withErrors(['error' => 'Tidak dapat menonaktifkan Super Admin aktif satu-satunya.']);
            }
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri saat sedang masuk.']);
        }

        if ($user->isSuperAdmin()) {
            $superAdminsCount = User::where(fn ($q) => $q->where('role', 'super_admin')->orWhereHas('roleModel', fn ($r) => $r->where('name', 'super_admin')))->count();

            if ($superAdminsCount <= 1) {
                return back()->withErrors(['error' => 'Tidak dapat menghapus Super Admin terakhir di dalam sistem.']);
            }
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Pengguna {$name} telah berhasil dihapus dari sistem.");
    }
}
