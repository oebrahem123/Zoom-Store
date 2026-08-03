<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthorizationService
{
    public function ensureAuthenticatedAdmin(): void
    {
        if (!Auth::guard('admin')->check()) {
            abort(401, __('Unauthenticated.'));
        }
    }

    public function ensureSuperAdmin(): void
    {
        $this->ensureAuthenticatedAdmin();

        $user = Auth::guard('admin')->user();

        if (!$user instanceof User || !$user->role?->isSuperAdmin()) {
            abort(403, __('Unauthorized action. Only Super Admin can perform this action.'));
        }
    }
}
