<?php

namespace App\Services\Shipment;

use App\Models\Order;
use App\Models\ShipmentGroup;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Collection;

class ShipmentMergeService
{
    private const EDITABLE_STATUSES = ['pending', 'pending_review'];
    private const LOCKED_STATUSES = ['approved', 'processing', 'printing', 'shipped', 'delivered', 'cancelled'];
    private const SHIPPING_FEE = 80;

    public function getEditableShipments(User $user): Collection
    {
        return ShipmentGroup::where('user_id', $user->id)
            ->whereHas('orders', function ($q) {
                $q->whereIn('status', self::EDITABLE_STATUSES);
            })
            ->whereDoesntHave('orders', function ($q) {
                $q->whereIn('status', self::LOCKED_STATUSES);
            })
            ->withCount(['orders', 'orders as products_count' => function ($q) {
                $q->withCount('orderdetails');
            }])
            ->with(['orders' => function ($q) {
                $q->whereIn('status', self::EDITABLE_STATUSES);
            }])
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn($s) => $this->isEditable($s));
    }

    public function isEditable(ShipmentGroup $shipment): bool
    {
        if ($shipment->relationLoaded('orders')) {
            $orders = $shipment->orders;
        } else {
            $orders = $shipment->orders()->get();
        }

        if ($orders->isEmpty()) {
            return false;
        }

        return $orders->every(fn(Order $o) => in_array($o->status, self::EDITABLE_STATUSES, true));
    }

    public function validateRaceCondition(ShipmentGroup $shipment): bool
    {
        $shipment->refresh();
        $shipment->load('orders');
        return $this->isEditable($shipment);
    }

    public function executeMerge(ShipmentGroup $shipment, Order $newOrder): void
    {
        $newOrder->shipment_group_id = $shipment->id;
        $newOrder->shipping_cost = 0;
        $newOrder->shipping_saved = self::SHIPPING_FEE;
        $newOrder->save();

        $existingOrdersCount = $shipment->orders()->count();

        if ($existingOrdersCount === 1) {
            $firstOrder = $shipment->orders()->oldest()->first();
            if ($firstOrder && $firstOrder->id !== $newOrder->id) {
                $firstOrder->shipping_cost = self::SHIPPING_FEE;
                $firstOrder->shipping_saved = 0;
                $firstOrder->save();
            } elseif ($firstOrder && $firstOrder->id === $newOrder->id) {
                $firstOrder->shipping_cost = self::SHIPPING_FEE;
                $firstOrder->shipping_saved = 0;
                $firstOrder->save();
            }
        }

        $newOrder->load('orderdetails');
        $hasDesign = $newOrder->orderdetails->contains(fn($d) => !is_null($d->design_id));
        $initialStatus = $hasDesign ? 'pending_review' : 'pending';
        if ($newOrder->status !== $initialStatus) {
            $newOrder->update(['status' => $initialStatus]);
        }

        AuditLogService::log(
            action: 'order_merged_into_shipment',
            auditable: $shipment,
            description: 'إضافة الطلب #' . $newOrder->id . ' إلى الشحنة #' . $shipment->id,
            newValues: [
                'shipment_id' => $shipment->id,
                'order_id' => $newOrder->id,
                'user_id' => $newOrder->user_id,
            ],
        );
    }

    public function createShipmentWithOrder(Order $newOrder): ShipmentGroup
    {
        $group = ShipmentGroup::create([
            'user_id' => $newOrder->user_id,
            'status' => 'open',
        ]);

        $newOrder->shipment_group_id = $group->id;
        $newOrder->shipping_cost = self::SHIPPING_FEE;
        $newOrder->shipping_saved = 0;
        $newOrder->save();

        AuditLogService::log(
            action: 'shipment_created',
            auditable: $group,
            description: 'إنشاء شحنة جديدة #' . $group->id . ' للطلب #' . $newOrder->id,
            newValues: [
                'shipment_id' => $group->id,
                'order_id' => $newOrder->id,
                'user_id' => $newOrder->user_id,
            ],
        );

        return $group;
    }
}
