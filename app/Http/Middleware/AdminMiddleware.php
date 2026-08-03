<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::debug('[AUDIT_ROUTE] AdminMiddleware fired', [
            'uri' => $request->path(),
            'route_name' => $request->route() ? $request->route()->getName() : 'N/A',
            'route_uri' => $request->route() ? $request->route()->uri() : 'N/A',
            'middleware' => $request->route() ? implode('|', $request->route()->middleware()) : 'N/A',
            'authenticated' => Auth::guard('admin')->check(),
            'file' => 'web.php or admin.php (AdminMiddleware)',
        ]);

        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
