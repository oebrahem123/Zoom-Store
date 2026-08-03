<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PermissionService
{
    protected ?User $cachedUser = null;
    protected ?array $cachedPermissions = null;
    protected ?bool $cachedIsSuperAdmin = null;

    protected function user(): ?User
    {
        if ($this->cachedUser === null) {
            $this->cachedUser = Auth::guard('admin')->user();
        }
        return $this->cachedUser;
    }

    protected function isSuperAdmin(): bool
    {
        if ($this->cachedIsSuperAdmin === null) {
            $user = $this->user();
            $this->cachedIsSuperAdmin = $user && $user->role?->isSuperAdmin();
        }
        return $this->cachedIsSuperAdmin;
    }

    protected function resolvePermissions(): array
    {
        if ($this->cachedPermissions !== null) {
            return $this->cachedPermissions;
        }

        $user = $this->user();
        if (!$user) {
            $this->cachedPermissions = [];
            return $this->cachedPermissions;
        }

        if ($this->isSuperAdmin()) {
            $this->cachedPermissions = Permission::pluck('key')->all();
            return $this->cachedPermissions;
        }

        $rolePermissions = $user->role->permissions()->pluck('key')->toArray();

        $overrides = $user->userPermissions()
            ->with('permission')
            ->get();

        $allowed = [];
        $denied = [];

        foreach ($overrides as $override) {
            if ($override->type === 'allow') {
                $allowed[] = $override->permission->key;
            } else {
                $denied[] = $override->permission->key;
            }
        }

        $result = array_diff($rolePermissions, $denied);
        $result = array_merge($result, $allowed);

        $this->cachedPermissions = array_unique($result);
        return $this->cachedPermissions;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->resolvePermissions(), true);
    }

    public function ensurePermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            abort(403, __('Unauthorized. You do not have the required permission.'));
        }
    }

    public function hasAnyPermission(array $permissions): bool
    {
        $userPermissions = $this->resolvePermissions();
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions, true)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        $userPermissions = $this->resolvePermissions();
        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions, true)) {
                return false;
            }
        }
        return true;
    }

    public function getPermissionsForUser(User $user): array
    {
        if ($user->role?->isSuperAdmin()) {
            return Permission::pluck('key')->all();
        }

        $rolePermissions = $user->role->permissions()->pluck('key')->toArray();

        $overrides = $user->userPermissions()
            ->with('permission')
            ->get();

        $allowed = [];
        $denied = [];

        foreach ($overrides as $override) {
            if ($override->type === 'allow') {
                $allowed[] = $override->permission->key;
            } else {
                $denied[] = $override->permission->key;
            }
        }

        $result = array_diff($rolePermissions, $denied);
        $result = array_merge($result, $allowed);

        return array_unique($result);
    }

    public function clearCache(): void
    {
        $this->cachedUser = null;
        $this->cachedPermissions = null;
        $this->cachedIsSuperAdmin = null;
    }
}
