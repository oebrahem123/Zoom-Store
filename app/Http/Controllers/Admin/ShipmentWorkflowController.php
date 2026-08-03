<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShipmentGroup;
use App\Services\Workflow\ShipmentWorkflowPolicy;
use App\Services\Workflow\ShipmentWorkflowService;
use Illuminate\Http\Request;

class ShipmentWorkflowController extends Controller
{
    public function __construct(
        protected ShipmentWorkflowService $workflowService,
    ) {}

    public function __invoke(Request $request, ShipmentGroup $shipment)
    {
        $request->validate([
            'action' => 'required|string',
        ]);

        $action = $request->input('action');

        $allActions = array_merge(
            array_keys(ShipmentWorkflowPolicy::ACTION_STATUS_MAP),
            ShipmentWorkflowPolicy::SPECIAL_ACTIONS,
        );

        if (!in_array($action, $allActions, true)) {
            return back()->with('error', "الإجراء '{$action}' غير معروف");
        }

        $result = $this->workflowService->execute($shipment, $action);

        $message = "تم تنفيذ '{$result['action']}' على الشحنة #{$shipment->id}: ";
        $message .= "{$result['updated']} طلب محدث";
        if ($result['skipped'] > 0) {
            $message .= "، {$result['skipped']} طلب متخطى";
        }

        if (!empty($result['errors'])) {
            $message .= '، مع أخطاء: ' . implode(' | ', $result['errors']);
            return back()->with('error', $message);
        }

        if ($result['updated'] === 0) {
            return back()->with('error', "لم يتم تحديث أي طلب — جميع الطلبات في حالة لا تسمح بهذا الإجراء");
        }

        return back()->with('success', $message);
    }
}
