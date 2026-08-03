<?php

/**
 * ------------------------------------------------------------
 * Zoom Store
 * Sprint 2
 *
 * Purpose:
 * Dashboard quick actions — pending reviews, low stock, late orders
 *
 * Depends on:
 * Order, ProductVariant (read-only)
 *
 * Safe:
 * Read only. Each count is independently wrapped in try/catch.
 * ------------------------------------------------------------
 */

namespace App\Services\Dashboard;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;

class QuickActionsService
{
    public static function make(): array
    {
        try {
            $pendingReviewsCount = Order::where('status', 'pending_review')->count();
        } catch (\Throwable $e) {
            Log::warning('QuickActionsService::pendingReviews: '.$e->getMessage());
            $pendingReviewsCount = 0;
        }

        try {
            $lowStockCount = ProductVariant::where('quantity', '<=', 5)->count();
        } catch (\Throwable $e) {
            Log::warning('QuickActionsService::lowStock: '.$e->getMessage());
            $lowStockCount = 0;
        }

        try {
            $lateOrdersCount = Order::where('created_at', '<', now()->subDays(7))
                ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
                ->count();
        } catch (\Throwable $e) {
            Log::warning('QuickActionsService::lateOrders: '.$e->getMessage());
            $lateOrdersCount = 0;
        }

        return [
            'actions' => [
                'pendingReviewsCount' => $pendingReviewsCount,
                'lowStockCount'       => $lowStockCount,
                'lateOrdersCount'     => $lateOrdersCount,
            ],
        ];
    }
}
