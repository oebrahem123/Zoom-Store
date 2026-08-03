<?php

namespace App\Services\Workflow;

use App\Models\Order;
use App\Models\ShipmentGroup;
use App\Models\User;
use App\Services\Order\OrderStatusService;
use Illuminate\Support\Collection;

class ShipmentWorkflowPolicy
{
    private const TERMINAL_STATUSES = ['delivered', 'cancelled'];

    /** Map user-facing workflow actions to the target order status. */
    const ACTION_STATUS_MAP = [
        'approve' => 'approved',
        'process' => 'processing',
        'print'   => 'printing',
        'ship'    => 'shipped',
        'deliver' => 'delivered',
    ];

    /** Actions that bypass the normal workflow — handled as special cases. */
    const SPECIAL_ACTIONS = ['cancel'];

    /** Human-readable Arabic labels for each action. */
    const ACTION_LABELS = [
        'approve' => 'اعتماد',
        'process' => 'تجهيز',
        'print'   => 'طباعة',
        'ship'    => 'شحن',
        'deliver' => 'توصيل',
        'cancel'  => 'إلغاء',
    ];

    public static function derive(ShipmentGroup $shipment): array
    {
        $statuses = $shipment->orders->map(fn($order) =>
            $order->status === 'cancelled' && $order->rejected_at !== null
                ? 'rejected'
                : $order->status
        );

        return WorkflowAggregator::evaluate($statuses);
    }

    public static function isTerminal(array $statusResult): bool
    {
        return in_array($statusResult['status'], self::TERMINAL_STATUSES, true);
    }

    public static function hasActiveShipments(User $user): bool
    {
        return ShipmentGroup::where('user_id', $user->id)
            ->whereHas('orders', function ($q) {
                $q->whereNotIn('status', self::TERMINAL_STATUSES);
            })
            ->exists();
    }

    public static function terminalStatuses(): array
    {
        return self::TERMINAL_STATUSES;
    }

    /**
     * Return the list of workflow actions that are currently applicable
     * to at least one order in the shipment.
     */
    public static function allowedActions(ShipmentGroup $shipment): array
    {
        $aggregate = self::derive($shipment);

        if ($aggregate['status'] === 'delivered') {
            return [];
        }

        $actions = [];

        foreach (self::ACTION_STATUS_MAP as $action => $targetStatus) {
            if ($shipment->orders->contains(fn(Order $o) =>
                !in_array($o->status, ['delivered', 'cancelled', $targetStatus], true)
                && OrderStatusService::canTransition($o, $targetStatus)
            )) {
                $actions[] = $action;
            }
        }

        if ($shipment->orders->contains(fn(Order $o) =>
            !in_array($o->status, ['delivered', 'cancelled'], true)
        )) {
            $actions[] = 'cancel';
        }

        return $actions;
    }

    public static function actionLabel(string $action): string
    {
        return self::ACTION_LABELS[$action] ?? $action;
    }
}
