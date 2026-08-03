<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        Log::debug('[AUDIT_ROUTE] CheckPermission middleware fired', [
            'uri' => $request->path(),
            'route_name' => $request->route() ? $request->route()->getName() : 'N/A',
            'route_uri' => $request->route() ? $request->route()->uri() : 'N/A',
            'middleware' => $request->route() ? implode('|', $request->route()->middleware()) : 'N/A',
            'required_permission' => $permission,
            'file' => 'web.php (permission middleware only exists in web.php)',
        ]);

        $this->permissionService->ensurePermission($permission);
        return $next($request);
    }
}
