<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmployeeService
{
    public function getEmployees(array $filters = []): LengthAwarePaginator
    {
        $query = User::employees()->with('role');

        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });

        $query->when($filters['role_id'] ?? null, function ($q, $roleId) {
            $q->where('role_id', $roleId);
        });

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->latest()->paginate();
    }

    public function createEmployee(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $data['is_active'] = true;

            return User::create($data);
        });
    }

    public function updateEmployee(User $user, array $data, User $authUser): User
    {
        return DB::transaction(function () use ($user, $data, $authUser) {
            if ($user->id === $authUser->id && isset($data['role_id']) && (int) $data['role_id'] !== $user->role_id) {
                throw ValidationException::withMessages([
                    'role_id' => __('You cannot change your own role.'),
                ]);
            }

            if (isset($data['role_id']) && $user->role->isSuperAdmin() && $user->is_active && (int) $data['role_id'] !== $user->role_id) {
                $this->ensureNotLastSuperAdmin($user);
            }

            if (isset($data['password']) && filled($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            return $user->fresh();
        });
    }

    public function toggleStatus(User $user, User $authUser): User
    {
        return DB::transaction(function () use ($user, $authUser) {
            if ($user->id === $authUser->id) {
                throw ValidationException::withMessages([
                    'employee' => __('You cannot deactivate your own account.'),
                ]);
            }

            if ($user->is_active && $user->role->isSuperAdmin()) {
                $this->ensureNotLastSuperAdmin($user);
            }

            $user->update(['is_active' => !$user->is_active]);

            return $user->fresh();
        });
    }

    public function saveEmployeePermissions(User $user, array $permissionIds): void
    {
        DB::transaction(function () use ($user, $permissionIds) {
            $user->userPermissions()->delete();

            $rolePermissionIds = $user->role->permissions()->pluck('permissions.id')->toArray();

            $allows = [];
            $denies = [];

            foreach ($permissionIds as $permId) {
                $permId = (int) $permId;
                if (!in_array($permId, $rolePermissionIds)) {
                    $allows[] = ['user_id' => $user->id, 'permission_id' => $permId, 'type' => 'allow'];
                }
            }

            $rolePermsToDeny = array_diff($rolePermissionIds, $permissionIds);
            foreach ($rolePermsToDeny as $permId) {
                $denies[] = ['user_id' => $user->id, 'permission_id' => $permId, 'type' => 'deny'];
            }

            $records = array_merge($allows, $denies);

            foreach ($records as &$record) {
                $record['created_at'] = now();
                $record['updated_at'] = now();
            }

            if (!empty($records)) {
                UserPermission::insert($records);
            }
        });
    }

    protected function ensureNotLastSuperAdmin(User $user): void
    {
        $activeSuperAdmins = User::where('role_id', $user->role_id)
            ->where('is_active', true)
            ->count();

        if ($activeSuperAdmins <= 1) {
            throw ValidationException::withMessages([
                'employee' => __('Cannot modify the last active Super Admin.'),
            ]);
        }
    }
}
