<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\ShipmentGroup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrderService
{
    public function getOrders(int $userId): LengthAwarePaginator
    {
        return Order::with(['orderdetails', 'shipmentGroup'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function getOrderDetail(int $orderId, int $userId): ?Order
    {
        return Order::with([
            'orderdetails.product',
            'orderdetails.variant',
            'orderdetails.design',
            'shipmentGroup.orders',
            'timeline',
        ])
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    }

    public function computeTotals(Order $order): array
    {
        $itemsSubtotal = $order->orderdetails->sum(fn($d) => $d->lineTotal());
        $shippingCost = (float) ($order->shipping_cost ?? 0);
        $shippingSaved = (float) ($order->shipping_saved ?? 0);
        $grandTotal = $itemsSubtotal + $shippingCost;

        return [
            'items_subtotal' => $itemsSubtotal,
            'shipping_cost' => $shippingCost,
            'shipping_saved' => $shippingSaved,
            'grand_total' => $grandTotal,
        ];
    }

    public function hasDesignProducts(Order $order): bool
    {
        return $order->orderdetails->contains(fn($d) => !is_null($d->design_id));
    }
}
