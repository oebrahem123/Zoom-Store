<?php

namespace App\Services\Workflow;

use App\Models\ShipmentGroup;
use App\Services\Order\OrderStatusService;
use App\Services\Order\OrderTimelineService;

class ShipmentWorkflowService
{
    /**
     * Execute a workflow action on every eligible order in the shipment.
     *
     * @return array{action: string, updated: int, skipped: int, errors: array}
     */
    public function execute(ShipmentGroup $shipment, string $action): array
    {
        if (isset(ShipmentWorkflowPolicy::ACTION_STATUS_MAP[$action])) {
            return $this->executeStatusTransition(
                $shipment,
                $action,
                ShipmentWorkflowPolicy::ACTION_STATUS_MAP[$action]
            );
        }

        if ($action === 'cancel') {
            return $this->executeCancel($shipment);
        }

        throw new \InvalidArgumentException("Unknown workflow action: {$action}");
    }

    private function executeStatusTransition(
        ShipmentGroup $shipment,
        string $action,
        string $targetStatus,
    ): array {
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($shipment->orders as $order) {
            if (in_array($order->status, ['delivered', 'cancelled', $targetStatus], true)) {
                $skipped++;
                continue;
            }

            if (!OrderStatusService::canTransition($order, $targetStatus)) {
                $skipped++;
                continue;
            }

            try {
                $oldStatus = $order->status;
                \Log::debug('[AUDIT_ORDER_STATUS] ShipmentWorkflowService::executeStatusTransition', [
                    'order_id' => $order->id,
                    'from' => $oldStatus,
                    'to' => $targetStatus,
                    'controller' => 'ShipmentWorkflowService',
                    'file' => 'Services/Workflow/ShipmentWorkflowService.php',
                    'has_canTransition_check' => true,
                    'shipment_id' => $shipment->id,
                    'action' => $action,
                ]);
                $order->update(['status' => $targetStatus]);
                OrderTimelineService::log(
                    $order,
                    $targetStatus,
                    $oldStatus,
                    "تحديث {$action} عبر الشحنة #{$shipment->id}"
                );
                $updated++;
            } catch (\Throwable $e) {
                $errors[] = "الطلب #{$order->id}: {$e->getMessage()}";
            }
        }

        return [
            'action'  => $action,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors'  => $errors,
        ];
    }

    private function executeCancel(ShipmentGroup $shipment): array
    {
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($shipment->orders as $order) {
            if (in_array($order->status, ['delivered', 'cancelled'], true)) {
                $skipped++;
                continue;
            }

            try {
                $oldStatus = $order->status;
                \Log::debug('[AUDIT_ORDER_STATUS] ShipmentWorkflowService::executeCancel', [
                    'order_id' => $order->id,
                    'from' => $oldStatus,
                    'to' => 'cancelled',
                    'controller' => 'ShipmentWorkflowService',
                    'file' => 'Services/Workflow/ShipmentWorkflowService.php',
                    'has_canTransition_check' => false,
                    'shipment_id' => $shipment->id,
                ]);
                $order->update(['status' => 'cancelled']);
                OrderTimelineService::log(
                    $order,
                    'cancelled',
                    $oldStatus,
                    "إلغاء عبر الشحنة #{$shipment->id}"
                );
                $updated++;
            } catch (\Throwable $e) {
                $errors[] = "الطلب #{$order->id}: {$e->getMessage()}";
            }
        }

        return [
            'action'  => 'cancel',
            'updated' => $updated,
            'skipped' => $skipped,
            'errors'  => $errors,
        ];
    }
}
