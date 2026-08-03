<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\orderdetails;
use App\Services\Audit\AuditLogService;

class OrderResubmitService
{
    public static function resubmit(orderdetails $detail): Order
    {
        $order = Order::findOrFail($detail->order_id);
        $design = $detail->design;

        if (!$order->isRejected()) {
            throw new \Exception('الطلب غير مرفوض');
        }

        if (!$design) {
            throw new \Exception('لا يوجد تصميم مرتبط');
        }

        // Temporary implementation.
        // Current workflow operates at Order level.
        // Future Sprint will migrate this transition to per-product workflow.
        if (!$design->updated_at || !$order->rejected_at || $design->updated_at <= $order->rejected_at) {
            throw new \Exception('يجب تعديل التصميم أولاً قبل إعادة التقديم');
        }

        $oldStatus = $order->status;
        $oldRejectedAt = $order->rejected_at?->toDateTimeString();

        \Log::debug('[AUDIT_ORDER_STATUS] OrderResubmitService::resubmit', [
            'order_id' => $order->id,
            'from' => $oldStatus,
            'to' => 'pending_review',
            'controller' => 'OrderResubmitService::resubmit',
            'file' => 'Services/Order/OrderResubmitService.php',
            'has_canTransition_check' => false,
            'design_updated_at' => $design->updated_at?->toDateTimeString(),
            'rejected_at' => $oldRejectedAt,
        ]);

        $order->update([
            'status' => 'pending_review',
            'rejected_at' => null,
            'rejection_reason' => null,
            'rejection_category' => null,
        ]);

        OrderTimelineService::log(
            $order,
            'pending_review',
            'cancelled',
            'إعادة تقديم التصميم بعد الرفض'
        );

        AuditLogService::log(
            'resubmit_design',
            $order,
            'إعادة تقديم الطلب بعد الرفض',
            ['status' => $oldStatus, 'rejected_at' => $oldRejectedAt],
            ['status' => 'pending_review', 'rejected_at' => null],
        );

        return $order->fresh();
    }
}
