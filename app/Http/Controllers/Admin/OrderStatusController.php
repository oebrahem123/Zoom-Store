<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderStatusService;
use App\Services\Order\OrderTimelineService;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $newStatus = $request->status;

        if (!OrderStatusService::canTransition($order, $newStatus)) {
            return back()->with('error', 'حالة الطلب الحالية لا تسمح بهذا التغيير');
        }

        $oldStatus = $order->status;
        \Log::debug('[AUDIT_ORDER_STATUS] OrderStatusController@update', [
            'order_id' => $order->id,
            'from' => $oldStatus,
            'to' => $newStatus,
            'controller' => 'OrderStatusController@update',
            'file' => 'Admin/OrderStatusController.php',
            'has_canTransition_check' => true,
        ]);
        $order->update(['status' => $newStatus]);

        OrderTimelineService::log($order, $newStatus, $oldStatus, 'تحديث الحالة عبر لوحة التحكم');

        return back()->with('success', 'تم تحديث حالة الطلب رقم #' . $order->id . ' إلى "' . $newStatus . '" بنجاح');
    }
}
