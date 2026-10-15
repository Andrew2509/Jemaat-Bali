<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'nama',
        'role',
        'role_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function getRoleDisplayNameAttribute(): string
    {
        if ($this->roleModel) {
            return $this->roleModel->display_name;
        }

        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            default => ucfirst(str_replace('_', ' ', $this->role ?? 'Pengguna')),
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin'
            || ($this->roleModel && $this->roleModel->name === 'super_admin');
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        $current = $this->roleModel?->name ?? $this->role;

        return in_array($current, $roles, true);
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->is_active) {
            return false;
        }

        try {
            if ($this->roleModel) {
                return $this->roleModel->hasPermission($permissionName);
            }

            if ($this->role) {
                if (!property_exists($this, 'cachedStaticRole')) {
                    $this->cachedStaticRole = Role::where('name', $this->role)->first();
                }
                return $this->cachedStaticRole?->hasPermission($permissionName) ?? false;
            }
        } catch (\Throwable $e) {
            return $this->role === 'super_admin';
        }

        return false;
    }
}
