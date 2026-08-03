<?php

/**
 * ------------------------------------------------------------
 * Zoom Store
 * Sprint 2
 *
 * Purpose:
 * View paginated audit logs
 *
 * Safe:
 * Read-only. Does not modify any existing data.
 * ------------------------------------------------------------
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.audit_logs.index', compact('logs'));
    }
}
