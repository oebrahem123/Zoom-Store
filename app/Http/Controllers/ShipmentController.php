<?php

namespace App\Http\Controllers;

use App\Models\ShipmentGroup;
use App\Services\Shipment\CustomerShipmentViewService;

class ShipmentController extends Controller
{
    public function index()
    {
        $shipments = ShipmentGroup::with('orders')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        $statuses = $shipments->getCollection()->mapWithKeys(fn($s) => [
            $s->id => ShipmentWorkflowPolicy::derive($s),
        ]);

        return view('shipments.index', compact('shipments', 'statuses'));
    }

    public function show($id)
    {
        $shipment = ShipmentGroup::with([
            'orders.orderdetails.product',
            'orders.orderdetails.variant',
            'orders.orderdetails.design',
        ])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $prepared = app(CustomerShipmentViewService::class)->prepare($shipment);

        return view('shipments.show', array_merge(compact('shipment'), $prepared));
    }
}
