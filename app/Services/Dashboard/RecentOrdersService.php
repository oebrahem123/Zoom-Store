<?php

/**
 * ------------------------------------------------------------
 * Zoom Store
 * Sprint 2
 *
 * Purpose:
 * Last 10 orders for the admin dashboard
 *
 * Depends on:
 * Order, orderdetails, User (read-only)
 *
 * Safe:
 * Read only. Wrapped in try/catch for graceful failure.
 * ------------------------------------------------------------
 */

namespace App\Services\Dashboard;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class RecentOrdersService
{
    public static function make(): array
    {
        try {
            $recentOrders = Order::with(['orderdetails', 'user'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($o) {
                    return [
                        'id'           => $o->id,
                        'customer'     => $o->name ?? $o->user?->name ?? '—',
                        'status'       => $o->status,
                        'total'        => $o->orderdetails->sum(fn($d) => $d->lineTotal()),
                        'items_count'  => $o->orderdetails->sum('quantity'),
                        'created_at'   => $o->created_at,
                    ];
                });
        } catch (\Throwable $e) {
            Log::warning('RecentOrdersService: '.$e->getMessage());
            $recentOrders = collect();
        }

        return [
            'recentOrders' => $recentOrders,
        ];
    }
}
