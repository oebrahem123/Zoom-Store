<?php

/**
 * ------------------------------------------------------------
 * Zoom Store
 * Sprint 2
 *
 * Purpose:
 * Explicit audit logging service
 * NEVER automatic — only logs when intentionally called
 *
 * Depends on:
 * AuditLog model
 *
 * Safe:
 * Wrapped in try/catch. Missing table returns null without crashing.
 * ------------------------------------------------------------
 */

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    public static function log(
        string $action,
        ?Model $auditable = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): ?AuditLog {
        try {
            return AuditLog::create([
                'auditable_type' => $auditable ? get_class($auditable) : null,
                'auditable_id'   => $auditable?->getKey(),
                'user_id'        => auth()->id(),
                'action'         => $action,
                'description'    => $description,
                'old_values'     => $oldValues,
                'new_values'     => $newValues,
                'ip_address'     => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('AuditLogService::log failed: '.$e->getMessage());
            return null;
        }
    }
}
