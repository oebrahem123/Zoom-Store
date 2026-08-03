<?php

namespace App\Services\Shipment;

use App\Models\ShipmentGroup;
use App\Services\Workflow\ShipmentWorkflowPolicy;
use Illuminate\Support\Collection;

class CustomerShipmentViewService
{
    public function prepare(ShipmentGroup $shipment): array
    {
        $status = ShipmentWorkflowPolicy::derive($shipment);
        $groups = $this->enrichGroups($shipment);

        return [
            'status' => $status,
            'groups' => $groups,
            'aggregatedTimeline' => $this->aggregatedTimeline($shipment),
            'totals' => $this->computeTotals($shipment, $groups),
            'shippingInfo' => $this->shippingInfo($shipment),
            'trackingData' => $this->trackingData($shipment),
        ];
    }

    private function aggregatedTimeline(ShipmentGroup $shipment): Collection
    {
        $allTimelines = $shipment->orders->loadMissing('timeline')
            ->flatMap(fn($o) => $o->timeline)
            ->sortBy('created_at')
            ->values();

        return $allTimelines;
    }

    private function enrichGroups(ShipmentGroup $shipment): Collection
    {
        $statusLabelMap = [
            'cancelled'  => ['class' => 'badge-cancelled',  'label' => 'مرفوض'],
            'approved'   => ['class' => 'badge-approved',   'label' => 'مقبول'],
            'processing' => ['class' => 'badge-processing', 'label' => 'قيد التجهيز'],
            'printing'   => ['class' => 'badge-printing',   'label' => 'قيد الطباعة'],
            'shipped'    => ['class' => 'badge-shipped',    'label' => 'تم الشحن'],
            'delivered'  => ['class' => 'badge-delivered',  'label' => 'تم التوصيل'],
        ];
        $defaultStatus = ['class' => 'badge-pending', 'label' => 'قيد التنفيذ'];

        $stepOrder = ['pending' => 0, 'pending_review' => 1, 'approved' => 2, 'processing' => 3, 'printing' => 4, 'shipped' => 5, 'delivered' => 6];

        $groups = ShipmentProductGrouper::group($shipment);

        $groups = $groups->map(fn($group) => [
            'order' => $group['order'],
            'details' => $group['details'],
            'hasDesign' => $group['details']->contains(fn($d) => !is_null($d->design_id)),
            'currentStatus' => $statusLabelMap[$group['order']->status] ?? $defaultStatus,
            'states' => $group['details']->map(fn($d) => [
                'has_design' => !is_null($d->design_id),
                'workflow' => $group['order']->status,
                'is_rejected' => $group['order']->isRejected(),
                'rejection' => [
                    'reason' => $group['order']->rejection_reason,
                    'category' => $group['order']->rejection_category,
                    'category_label' => $group['order']->rejectionCategoryLabel(),
                    'rejected_at' => $group['order']->rejected_at,
                ],
                'design_preview' => $d->design?->preview_image,
                'design_updated_at' => $d->design?->updated_at,
                'availability' => $d->catalogStatus(),
                'shipment_group' => $shipment->id,
            ]),
        ]);

        return $groups->sortByDesc(fn($g) => $stepOrder[$g['order']->status] ?? -1)->values();
    }

    private function computeTotals(ShipmentGroup $shipment, Collection $groups): array
    {
        $totals = [
            'products_count' => $groups->sum(fn($g) => $g['details']->count()),
            'orders_count' => $groups->count(),
            'subtotal' => $groups->sum(fn($g) => $g['details']->sum(fn($d) => $d->lineTotal())),
            'shipping_cost' => $shipment->orders->sum('shipping_cost'),
            'shipping_saved' => $shipment->orders->sum('shipping_saved'),
        ];
        $totals['grand_total'] = $totals['subtotal'] + $totals['shipping_cost'];
        return $totals;
    }

    private function shippingInfo(ShipmentGroup $shipment): array
    {
        $firstOrder = $shipment->orders->first();
        return $firstOrder ? [
            'name'    => $firstOrder->name,
            'phone'   => $firstOrder->phone,
            'address' => $firstOrder->address,
            'notes'   => $firstOrder->note,
        ] : ['name' => '—', 'phone' => '—', 'address' => '—', 'notes' => '—'];
    }

    private function trackingData(ShipmentGroup $shipment): array
    {
        $firstOrder = $shipment->orders->first();
        return ['address' => $firstOrder ? $firstOrder->address : ''];
    }
}
