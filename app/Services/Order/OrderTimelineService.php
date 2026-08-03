<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderTimeline;

class OrderTimelineService
{
    public static function log(
        Order $order,
        string $toStatus,
        ?string $fromStatus = null,
        ?string $notes = null,
    ): OrderTimeline {
        return OrderTimeline::create([
            'order_id'    => $order->id,
            'from_status' => $fromStatus,
            'to_status'   => $toStatus,
            'notes'       => $notes,
            'user_id'     => auth()->id(),
        ]);
    }

    public static function getTimeline(Order $order)
    {
        return $order->timeline()->latest()->get();
    }
}
