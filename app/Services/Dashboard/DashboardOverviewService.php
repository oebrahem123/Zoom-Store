<?php

/**
 * ------------------------------------------------------------
 * Zoom Store
 * Sprint 2
 *
 * Purpose:
 * Dashboard statistics and summary cards
 * Single responsibility: return overview counts
 *
 * Depends on:
 * Order, orderdetails (read-only)
 *
 * Safe:
 * Read only. Wraps queries in try/catch for graceful failure.
 * ------------------------------------------------------------
 */

namespace App\Services\Dashboard;

use App\Models\Order;
use App\Models\orderdetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardOverviewService
{
    public static function make(): array
    {
        try {
            $pendingOrdersCount = Order::whereIn('status', ['pending', 'pending_review'])->count();
        } catch (\Throwable $e) {
            Log::warning('DashboardOverviewService::pendingOrdersCount: '.$e->getMessage());
            $pendingOrdersCount = 0;
        }

        try {
            $revenueToday = orderdetails::whereDate('created_at', today())
                ->sum(DB::raw('price * quantity'));
        } catch (\Throwable $e) {
            Log::warning('DashboardOverviewService::revenueToday: '.$e->getMessage());
            $revenueToday = 0;
        }

        try {
            $ordersTodayCount = Order::whereDate('created_at', today())->count();
        } catch (\Throwable $e) {
            Log::warning('DashboardOverviewService::ordersTodayCount: '.$e->getMessage());
            $ordersTodayCount = 0;
        }

        try {
            $statusDistribution = Order::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->orderBy('total', 'desc')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('DashboardOverviewService::statusDistribution: '.$e->getMessage());
            $statusDistribution = collect();
        }

        return [
            'overview' => [
                'pendingOrdersCount' => $pendingOrdersCount,
                'revenueToday'       => $revenueToday,
                'ordersTodayCount'   => $ordersTodayCount,
                'statusDistribution' => $statusDistribution,
            ],
        ];
    }
}
