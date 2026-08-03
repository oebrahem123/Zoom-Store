<?php

namespace App\Services\Order;

use App\Models\Order;

class OrderStatusService
{
    const DESIGN_WORKFLOW = [
        'pending_review',
        'approved',
        'processing',
        'printing',
        'shipped',
        'delivered',
    ];

    const NORMAL_WORKFLOW = [
        'pending',
        'processing',
        'shipped',
        'delivered',
    ];

    public static function getWorkflow(Order $order): array
    {
        $hasDesign = $order->orderdetails->contains(fn($d) => !is_null($d->design_id));
        return $hasDesign ? self::DESIGN_WORKFLOW : self::NORMAL_WORKFLOW;
    }

    public static function initialStatus(Order $order): string
    {
        $hasDesign = $order->orderdetails->contains(fn($d) => !is_null($d->design_id));
        return $hasDesign ? 'pending_review' : 'pending';
    }

    public static function nextStatuses(Order $order): array
    {
        $workflow = self::getWorkflow($order);
        $currentIndex = array_search($order->status, $workflow);

        if ($currentIndex === false || $currentIndex >= count($workflow) - 1) {
            return [];
        }

        return [$workflow[$currentIndex + 1]];
    }

    public static function canTransition(Order $order, string $newStatus): bool
    {
        if ($order->status === 'cancelled' || $order->status === 'delivered') {
            return false;
        }

        $next = self::nextStatuses($order);
        return in_array($newStatus, $next);
    }
}
