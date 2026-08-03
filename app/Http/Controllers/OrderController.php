<?php

namespace App\Http\Controllers;

use App\Models\ShipmentGroup;
use App\Services\Order\OrderService;
use App\Services\Shipment\CustomerShipmentViewService;
use App\Services\Workflow\ShipmentWorkflowPolicy;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index()
    {
        $userId = auth()->id();
        $service = app(CustomerShipmentViewService::class);

        $shipments = ShipmentGroup::where('user_id', $userId)
            ->with([
                'orders.orderdetails.product',
                'orders.orderdetails.variant',
                'orders.orderdetails.design',
            ])
            ->get()
            ->filter(fn($s) => $s->orders->contains(
                fn($o) => !in_array($o->status, ShipmentWorkflowPolicy::terminalStatuses(), true)
            ))
            ->sortByDesc('created_at')
            ->values();

        $prepared = [];
        foreach ($shipments as $s) {
            $prepared[$s->id] = $service->prepare($s);
        }

        return view('orders.index', compact('shipments', 'prepared'));
    }

    public function show($id)
    {
        $order = $this->orderService->getOrderDetail($id, auth()->id());

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'الطلب غير موجود');
        }

        $totals = $this->orderService->computeTotals($order);
        $hasDesign = $this->orderService->hasDesignProducts($order);
        $timeline = $order->timeline;

        return view('orders.show', compact('order', 'totals', 'hasDesign', 'timeline'));
    }
}
